package com.scala.hrm.audit;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.boot.web.servlet.support.SpringBootServletInitializer;


@SpringBootApplication
public class HRMAuditApplication extends SpringBootServletInitializer {

	public static void main(String[] args) {
		SpringApplication.run(HRMAuditApplication.class, args);
	}

}
