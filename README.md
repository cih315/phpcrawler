PHP多进程/多线程爬虫 

	1、基于swoole扩展

	2、抓取目前可采用curl或者snoopy 

	3、解析数据目前可使用 dom解析

	其余类库会进一步支持

使用说明 

	1、安装swoole扩展  最新版本即可
	   sudo pecl install swoole  

	2、安装redis扩展
	   sudo apt-get install redis-server 
	
	3、配置config/site.php 
	
	4、php start_crawl.php  
	
	5、php start_parse.php



目前只是基本功能实现，基本都不支持自定义功能。只支持在CLI下运行

