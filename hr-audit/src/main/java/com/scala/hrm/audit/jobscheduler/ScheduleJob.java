package com.scala.hrm.audit.jobscheduler;

import org.apache.log4j.Logger;
import org.quartz.CronScheduleBuilder;
import org.quartz.JobBuilder;
import org.quartz.JobDetail;
import org.quartz.Scheduler;
import org.quartz.Trigger;
import org.quartz.TriggerBuilder;
import org.quartz.impl.StdSchedulerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.context.annotation.PropertySource;
import org.springframework.context.event.ContextRefreshedEvent;
import org.springframework.context.event.EventListener;
import org.springframework.stereotype.Component;

import com.scala.hrm.audit.job.FilesAuditJob;

@Component
@PropertySource("classpath:job.properties")
public class ScheduleJob {
	
	protected static Logger logger = Logger.getLogger(ScheduleJob.class.getName());
	
	@Value("${job.audit}")
	private String auditJobSchedule;
	
	@EventListener(ContextRefreshedEvent.class)
	  public void contextRefreshedEvent() {
		schedulePurgeJob(); 
	  }
	
	private void schedulePurgeJob()
	{
		logger.info("In audit data Job");
		try {
			JobDetail job = JobBuilder.newJob(FilesAuditJob.class)
			        .withIdentity("FilesAuditJob", "Scala").build();
			
			Trigger trigger = TriggerBuilder
			        .newTrigger()
			        .withIdentity("FilesAuditJob", "Scala")
			        .withSchedule(
			            CronScheduleBuilder.cronSchedule(auditJobSchedule))
			        .build();
			
			Scheduler scheduler = new StdSchedulerFactory().getScheduler();
	    	scheduler.start();
	    	scheduler.scheduleJob(job, trigger);
	    	
		} catch (Exception e) {
			logger.error(e.getMessage());
		}
	}
}
