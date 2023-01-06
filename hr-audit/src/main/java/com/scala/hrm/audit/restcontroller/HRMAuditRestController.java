package com.scala.hrm.audit.restcontroller;


import org.apache.log4j.Logger;
import org.springframework.context.annotation.Bean;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.client.RestTemplate;


@RestController
@RequestMapping("/")
public class HRMAuditRestController {

	protected static Logger logger = Logger.getLogger(HRMAuditRestController.class.getName());

	@Bean
	public RestTemplate restTemplate() {
		return new RestTemplate();
	}
}