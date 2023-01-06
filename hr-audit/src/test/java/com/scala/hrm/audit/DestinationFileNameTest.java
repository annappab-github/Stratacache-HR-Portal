package com.scala.hrm.audit;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.nio.file.attribute.BasicFileAttributes;
import java.text.SimpleDateFormat;
import java.time.ZoneId;
import java.time.format.DateTimeFormatter;
import java.util.Date;

import org.apache.commons.io.FilenameUtils;

public class DestinationFileNameTest {
	
	public static void main(String args[]) throws IOException {
		
//		Date today = new Date();
//		SimpleDateFormat dateFormatter = new SimpleDateFormat("dd_MM_yyyy_HH_mm");
//		
//		String modifiedBy = "Manish Kumar";
//		String source = "e.m.p-apac.xlsx";
//		String extension = FilenameUtils.getExtension(source);
//		
//		System.out.println(source.substring(0, source.lastIndexOf('.'))+"_"+dateFormatter.format(today)+"_"+modifiedBy.replaceAll(" ", "_")+"."+extension);
//		
//		System.out.println(dateFormatter.format(today));
		
		DateTimeFormatter formatter = DateTimeFormatter.ofPattern("dd_MM_yyyy_HH_mm");
		String fileName = "C:\\ProgramData\\Scala\\hrm-portal\\assets\\employee\\pics\\IN025.png";
		Path file = Paths.get(fileName);
        BasicFileAttributes attr =
            Files.readAttributes(file, BasicFileAttributes.class);

        System.out.println("creationTime: " + attr.creationTime().toInstant().atZone(ZoneId.systemDefault()).format(formatter));
        System.out.println("lastAccessTime: " + attr.lastAccessTime().toInstant().atZone(ZoneId.systemDefault()).format(formatter));
        System.out.println("lastModifiedTime: " + attr.lastModifiedTime().toInstant().atZone(ZoneId.systemDefault()).format(formatter));
	}

}
