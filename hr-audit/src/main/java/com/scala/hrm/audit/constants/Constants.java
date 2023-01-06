package com.scala.hrm.audit.constants;

public class Constants {

	private Constants()
	{
		throw new IllegalStateException("Constants class");


	}

	public static final String APP_DIRECTORY = "C:\\ProgramData\\Scala\\hrm-portal";
	public static final String CHECKSUM_DIRECTORY = "C:\\ProgramData\\Scala\\hrm-portal\\assets\\checksum";
	public static final String AUDIT_DIRECTORY = "C:\\ProgramData\\Scala\\hrm-portal\\assets\\audits";
	public static final String CHECKSUM_FILE = "fileschecksum.json";
	public static final String[] EXCLUDED_DIRECTORIES = {"Logs", "audits", "checksum"};
	public static final String[] OFFICE_FILE_EXTENSIONS = {"xlsx", "docx"};
	public static final String AUDIT_POST_URL = "http://127.0.0.1:8000/api/audit";
	public static final int AUDIT_ID = 99999;
	public static final String AUDIT_EVENT_TYPE = "updated";
	public static final String TOKEN_URL = "http://127.0.0.1:8000/api/login";
	public static final String AUDIT_USERNAME = "hrmaudit";
	public static final String AUDIT_PASSWORD = "Scala@123";
}
