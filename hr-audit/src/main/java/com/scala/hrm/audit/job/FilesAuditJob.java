package com.scala.hrm.audit.job;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.nio.file.attribute.BasicFileAttributes;
import java.security.MessageDigest;
import java.text.SimpleDateFormat;
import java.time.ZoneId;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.List;

import org.apache.commons.io.FileUtils;
import org.apache.commons.io.FilenameUtils;
import org.apache.http.HttpEntity;
import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClientBuilder;
import org.apache.http.util.EntityUtils;
import org.apache.log4j.Logger;
import org.apache.poi.POIXMLProperties;
import org.apache.poi.openxml4j.opc.OPCPackage;
import org.apache.poi.openxml4j.opc.internal.PackagePropertiesPart;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;
import org.quartz.Job;
import org.quartz.JobExecutionContext;
import org.quartz.JobExecutionException;

import com.scala.hrm.audit.constants.Constants;

public class FilesAuditJob implements Job {
	
	protected static Logger logger = Logger.getLogger(FilesAuditJob.class.getName());
	JSONArray auditDataArray = new JSONArray();
	boolean fileWriteFlag = false;
	
	@Override
	public void execute(JobExecutionContext context) throws JobExecutionException {
		
		logger.info("-------------FilesAuditJob---------------");
		logger.info("----------------------------------------");
		logger.info("---------READ OLD DATA METHOD-----------");
		

		try 
		{
			String folderName = Constants.APP_DIRECTORY;
			List<String> fileList = getAllFilesinFolder(folderName,new ArrayList<String>());
			if(fileList.size() > 0) {
				for(String eachFile:fileList) {
					checkIfKeyExists(eachFile,toHexString(getMD5Checksum(eachFile)));
				}
			}
			
			if(fileWriteFlag) {
				sendAuditData();
			} else {
				System.out.println("No changes found since last audit!!!!");
			}
			
		}catch (FileNotFoundException e) {
			logger.error(e.getMessage());
		} catch (IOException e) {
			logger.error(e.getMessage());
		} catch (ParseException e) {
			logger.error(e.getMessage());
		} catch (Exception e) {
			logger.error(e.getMessage());
		}
	}
	
	private List<String> getAllFilesinFolder(String folderName, List<String> fileList) {
		List<String> excludedFolders = Arrays.asList(Constants.EXCLUDED_DIRECTORIES);
		File[] files = new File(folderName).listFiles();
		if(files != null) {
			for (File file : files) {
				if (file.isFile()) {
					fileList.add(file.getAbsolutePath());
				} else if (file.isDirectory() && !excludedFolders.contains(file.getName())) {
					getAllFilesinFolder(file.getAbsolutePath(), fileList);
				}
			}
		}
		return fileList;
	}
	
	private byte[] getMD5Checksum(String filename) throws Exception {
		InputStream fis = new FileInputStream(filename);
		MessageDigest md = MessageDigest.getInstance("MD5");
		byte[] buffer = new byte[1024];
		int aux;
		do {
			aux = fis.read(buffer);
			if (aux > 0) {
				md.update(buffer, 0, aux);
			}
		} while (aux != -1);
		fis.close();
		return md.digest();
	}
	
	private String toHexString(byte[] bytes) {
		StringBuilder sb = new StringBuilder(bytes.length * 2);
		for (byte b : bytes)
			sb.append(String.format("%02x", b & 0xff));
		return sb.toString();
	}
	
	@SuppressWarnings("unchecked")
	private boolean checkIfKeyExists(String key, String checksum) throws FileNotFoundException, IOException, ParseException {
		String filePath = Constants.CHECKSUM_DIRECTORY+"\\"+Constants.CHECKSUM_FILE;
		checkIfFileExists(filePath);
		JSONParser parser = new JSONParser();
		JSONObject json = (JSONObject) parser.parse(new FileReader(filePath));
		if(json.get(key) == null || !json.get(key).equals(checksum)) {
			json.put(key, checksum);
			fileWriteFlag = true;
			copyFile(key);
		}
		
		if(fileWriteFlag) {
			FileWriter file = new FileWriter(filePath);
			file.write(json.toJSONString());
			file.close();
		}
		return false;
	}
	
	private void checkIfFileExists(String filePath) {
		File file = new File(filePath);
		try {
			if(file.createNewFile())
			{
				FileWriter writer = new FileWriter(filePath);
				writer.write("{}");
				writer.close();
				System.out.println("File >>"+file +">> Does not exists creating a new file");
			}
		} catch (IOException e) {
			logger.error(e.getMessage());
		}
	}
	
	private void copyFile(String sourceFile) {
		File source = new File(sourceFile);
		String destFileName = getDestinationFileName(sourceFile, sourceFile.substring(sourceFile.lastIndexOf('\\') + 1));
		File dest = new File(Constants.AUDIT_DIRECTORY+"\\"+destFileName);
		try {
		    FileUtils.copyFile(source, dest);
		} catch (IOException e) {
			logger.error(e.getMessage());
		}
	}
	
	@SuppressWarnings("unchecked")
	private String getDestinationFileName(String source, String fileName) {
		List<String> officeFileExtensions = Arrays.asList(Constants.OFFICE_FILE_EXTENSIONS);
		String createdAt;
		String modifiedAt;
		String modifiedBy;
		try {
			String fileExtension = FilenameUtils.getExtension(fileName);
			if(officeFileExtensions.contains(fileExtension)) {
				OPCPackage pkg = OPCPackage.open(source);
				POIXMLProperties props = new POIXMLProperties(pkg);
				PackagePropertiesPart ppropsPart = props.getCoreProperties().getUnderlyingProperties();
				
				SimpleDateFormat dateFormatter = new SimpleDateFormat("dd_MM_yyyy_HH_mm");
				SimpleDateFormat auditDateFormatter = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
				
				Date created = ppropsPart.getCreatedProperty().getValue();
				Date modified = ppropsPart.getModifiedProperty().getValue();

				String lastModifiedBy = ppropsPart.getLastModifiedByProperty().getValue();
				createdAt = auditDateFormatter.format(created);
				modifiedAt = auditDateFormatter.format(modified);
				modifiedBy = lastModifiedBy;
				
//				System.out.println(dateFormatter.format(created)+">>>>>"+dateFormatter.format(modified)+">>>>>"+lastModifiedBy);
				auditDataArray.add(getFormattedJSONDataForAudit(modifiedBy, source, createdAt, modifiedAt));
//				sendAuditData(modifiedBy, source, createdAt, modifiedAt);
				
				return (fileName.substring(0, fileName.lastIndexOf('.'))+"_"+dateFormatter.format(modified)+"_"+lastModifiedBy.replaceAll(" ", "_")+"."+fileExtension);
			} else {
				DateTimeFormatter formatter = DateTimeFormatter.ofPattern("dd_MM_yyyy_HH_mm");
				DateTimeFormatter auditFormatter = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss");
				
				Path file = Paths.get(source);
		        BasicFileAttributes attr =
		            Files.readAttributes(file, BasicFileAttributes.class);

//		        System.out.println("creationTime: " + attr.creationTime().toInstant().atZone(ZoneId.systemDefault()).format(formatter));
//		        System.out.println("lastAccessTime: " + attr.lastAccessTime().toInstant().atZone(ZoneId.systemDefault()).format(formatter));
//		        System.out.println("lastModifiedTime: " + attr.lastModifiedTime().toInstant().atZone(ZoneId.systemDefault()).format(formatter));
		        
		        createdAt = attr.creationTime().toInstant().atZone(ZoneId.systemDefault()).format(auditFormatter);
				modifiedAt = attr.lastModifiedTime().toInstant().atZone(ZoneId.systemDefault()).format(auditFormatter);
				modifiedBy = null;
		        
				auditDataArray.add(getFormattedJSONDataForAudit(modifiedBy, source, createdAt, modifiedAt));
//				sendAuditData(modifiedBy, source, createdAt, modifiedAt);
				
		        return ((fileName.substring(0, fileName.lastIndexOf('.')))+"_"+(attr.lastModifiedTime().toInstant().atZone(ZoneId.systemDefault()).format(formatter))+"."+fileExtension);
			}
	        
		} catch (FileNotFoundException e) {
			logger.error(e.getMessage());
		} catch (IOException e) {
			logger.error(e.getMessage());
		} catch (Exception e) {
			logger.error(e.getMessage());
		} 
		
		return fileName;
	}
	
	@SuppressWarnings({ "unchecked" })
	private JSONObject getFormattedJSONDataForAudit(String user, String filePath, String created, String modified) {
		JSONObject auditJson = new JSONObject();
		auditJson.put("user_type", user);
		auditJson.put("auditable_type", filePath);
		auditJson.put("auditable_id", Constants.AUDIT_ID);
		auditJson.put("event", Constants.AUDIT_EVENT_TYPE);
		auditJson.put("created_at", created);
		auditJson.put("updated_at", modified);
		
		return auditJson;
	}
	
	private void sendAuditData() throws IOException {
		CloseableHttpClient httpClient = HttpClientBuilder.create().build();

		try {
		    HttpPost request = new HttpPost(Constants.AUDIT_POST_URL);
		    StringEntity params = new StringEntity(auditDataArray.toString());
		    request.addHeader("Authorization", "Bearer "+getToken());
		    request.addHeader("content-type", "application/json");
		    request.setEntity(params);
		    httpClient.execute(request);
		    System.out.println("Audit data sent sucessfully!!!!");
		} catch (Exception e) {
			logger.error(e.getMessage());
		} finally {
		    httpClient.close();
		}
	}
	
	@SuppressWarnings("unchecked")
	private String getToken() throws IOException {
		JSONObject tokenJson = new JSONObject();
		tokenJson.put("name", Constants.AUDIT_USERNAME);
		tokenJson.put("password", Constants.AUDIT_PASSWORD);
		
		CloseableHttpClient httpClient = HttpClientBuilder.create().build();

		try {
		    HttpPost request = new HttpPost(Constants.TOKEN_URL);
		    StringEntity params = new StringEntity(tokenJson.toString());
		    request.addHeader("content-type", "application/json");
		    request.setEntity(params);
		    CloseableHttpResponse response = httpClient.execute(request);
		    HttpEntity entity = response.getEntity();
		    String result = EntityUtils.toString(entity);
		    
		    JSONParser parser = new JSONParser();  
		    JSONObject responseJson = (JSONObject) parser.parse(result);  
		    
		    JSONObject data = (JSONObject) responseJson.get("data");
		    return (data.get("token")).toString();
		} catch (Exception e) {
			logger.error(e.getMessage());
		} finally {
		    httpClient.close();
		}
		
		return null;
	}
}