<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "notify".
 *
 * Auto generated 09-01-2013 01:31
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Notification and subscription',
	'description' => 'Provides everything necessary to control subscriptions to changes in any record or file, along with all the infrastructure to send out fully customized notifications in many different ways. Highly configurable.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.3.1',
	'dependencies' => 'cms,extbase,fluid,flux,fluidwidget,fed,vhs',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Claus Due',
	'author_email' => 'claus@wildside.dk',
	'author_company' => 'Wildside A/S',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'extbase' => '',
			'fluid' => '',
			'flux' => '',
			'fluidwidget' => '',
			'fed' => '',
			'vhs' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:74:{s:16:"ext_autoload.php";s:4:"27c1";s:12:"ext_icon.gif";s:4:"e922";s:17:"ext_localconf.php";s:4:"e166";s:14:"ext_tables.php";s:4:"9f5e";s:14:"ext_tables.sql";s:4:"dbb5";s:9:"README.md";s:4:"0a13";s:49:"Classes/Command/NotificationCommandController.php";s:4:"0a8c";s:54:"Classes/Communication/ShortMessageServiceInterface.php";s:4:"e319";s:45:"Classes/Controller/SubscriptionController.php";s:4:"e761";s:37:"Classes/Domain/Model/Subscription.php";s:4:"598a";s:38:"Classes/Domain/Model/UpdatedObject.php";s:4:"85b1";s:52:"Classes/Domain/Repository/SubscriptionRepository.php";s:4:"e1e2";s:41:"Classes/Extraction/ExtractorInterface.php";s:4:"a5ad";s:35:"Classes/Message/AbstractMessage.php";s:4:"01d1";s:44:"Classes/Message/DeliveryServiceInterface.php";s:4:"6565";s:30:"Classes/Message/FluidEmail.php";s:4:"9441";s:36:"Classes/Message/MessageInterface.php";s:4:"3dda";s:34:"Classes/Message/PlaintextEmail.php";s:4:"fb44";s:33:"Classes/Poller/AbstractPoller.php";s:4:"366b";s:29:"Classes/Poller/FilePoller.php";s:4:"b546";s:36:"Classes/Poller/PageContentPoller.php";s:4:"ef9d";s:34:"Classes/Poller/PollerInterface.php";s:4:"db2f";s:31:"Classes/Poller/RecordPoller.php";s:4:"66f2";s:43:"Classes/Scheduler/AbstractSchedulerTask.php";s:4:"7717";s:47:"Classes/Scheduler/NotificationSchedulerTask.php";s:4:"b21f";s:41:"Classes/Scheduler/SharedFieldProvider.php";s:4:"fd3e";s:35:"Classes/Service/AbstractService.php";s:4:"18cf";s:40:"Classes/Service/ConfigurationService.php";s:4:"c585";s:32:"Classes/Service/EmailService.php";s:4:"ea71";s:39:"Classes/Service/NotificationService.php";s:4:"ccea";s:39:"Classes/Service/SubscriptionService.php";s:4:"49c4";s:48:"Classes/Subscription/SourceProviderInterface.php";s:4:"964b";s:47:"Classes/Subscription/StandardSourceProvider.php";s:4:"ffd1";s:59:"Classes/UserFunction/TableColumnsItemProcessingFunction.php";s:4:"2ec9";s:60:"Classes/UserFunction/TableRecordsItemsProcessingFunction.php";s:4:"0b83";s:48:"Classes/ViewHelpers/ContentExtractViewHelper.php";s:4:"0c84";s:49:"Classes/ViewHelpers/ContentIdentityViewHelper.php";s:4:"9279";s:45:"Classes/ViewHelpers/ContentTypeViewHelper.php";s:4:"2485";s:40:"Classes/ViewHelpers/FieldsViewHelper.php";s:4:"1ab1";s:39:"Classes/ViewHelpers/ModesViewHelper.php";s:4:"382f";s:40:"Classes/ViewHelpers/TablesViewHelper.php";s:4:"11c6";s:41:"Classes/ViewHelpers/UcfirstViewHelper.php";s:4:"dde3";s:60:"Classes/ViewHelpers/Message/AbstractAttachmentViewHelper.php";s:4:"d01c";s:52:"Classes/ViewHelpers/Message/AttachmentViewHelper.php";s:4:"4d26";s:47:"Classes/ViewHelpers/Message/EmbedViewHelper.php";s:4:"05a9";s:47:"Classes/ViewHelpers/Message/ImageViewHelper.php";s:4:"073e";s:50:"Classes/ViewHelpers/Widget/SubscribeViewHelper.php";s:4:"6c82";s:61:"Classes/ViewHelpers/Widget/Controller/SubscribeController.php";s:4:"5db3";s:26:"Configuration/FlexForm.xml";s:4:"4265";s:34:"Configuration/TCA/Subscription.php";s:4:"ed9f";s:38:"Configuration/TypoScript/constants.txt";s:4:"d897";s:34:"Configuration/TypoScript/setup.txt";s:4:"a028";s:40:"Resources/Private/Language/locallang.xml";s:4:"bf41";s:80:"Resources/Private/Language/locallang_csh_tx_notify_domain_model_subscription.xml";s:4:"9292";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"3b24";s:38:"Resources/Private/Layouts/Default.html";s:4:"d250";s:37:"Resources/Private/Layouts/Message.eml";s:4:"a3d7";s:44:"Resources/Private/Layouts/PlaintextEmail.txt";s:4:"dff1";s:45:"Resources/Private/Partials/Email/Default.html";s:4:"84ec";s:55:"Resources/Private/Templates/Subscription/Component.html";s:4:"5dad";s:50:"Resources/Private/Templates/Subscription/List.html";s:4:"2b69";s:51:"Resources/Private/Templates/Subscription/Reset.html";s:4:"d41d";s:54:"Resources/Private/Templates/Subscription/Timeline.html";s:4:"775c";s:67:"Resources/Private/Templates/ViewHelpers/Widget/Subscribe/Index.html";s:4:"1674";s:71:"Resources/Private/Templates/ViewHelpers/Widget/Subscribe/Subscribe.html";s:4:"6d0c";s:68:"Resources/Private/Templates/ViewHelpers/Widget/Subscribe/Toggle.html";s:4:"6d0c";s:73:"Resources/Private/Templates/ViewHelpers/Widget/Subscribe/Unsubscribe.html";s:4:"6d0c";s:53:"Resources/Public/Icons/glyphicons-halflings-white.png";s:4:"1111";s:47:"Resources/Public/Icons/glyphicons-halflings.png";s:4:"531d";s:35:"Resources/Public/Icons/relation.gif";s:4:"e615";s:62:"Resources/Public/Icons/tx_notify_domain_model_subscription.gif";s:4:"905a";s:48:"Resources/Public/Javascripts/Subscribe.plugin.js";s:4:"9ddf";s:52:"Tests/Unit/Controller/SubscriptionControllerTest.php";s:4:"1480";s:44:"Tests/Unit/Domain/Model/SubscriptionTest.php";s:4:"3679";}',
);

?>