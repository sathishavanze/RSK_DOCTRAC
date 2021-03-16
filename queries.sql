ALTER TABLE tOrders ADD COLUMN OrderProductID int REFERENCES tOrderProduct(OrderProductID)


ALTER TABLE `tOrderProduct` ADD `ProductNumber` VARCHAR(100) NOT NULL AFTER `PackageUID`, ADD `CreatedDateTime` DATETIME NOT NULL AFTER `ProductNumber`;

ALTER TABLE `tOrderProduct`  ADD `CreatedByUserUID` int(11) NOT NULL REFERENCES mUsers(UserUID);

ALTER TABLE `mCustomer` ADD `OrganizationUID` int(11) NOT NULL REFERENCES mOrganization(OrganizationUID);

-- Replace LoginId with EmailId
UPDATE mUsers AS U1, mUsers AS U2 
SET U1.LoginID = U2.EmailID
WHERE U2.UserUID = U1.UserUID;


CREATE TABLE mUserPasswordVerification(PassVerifyUID int PRIMARY KEY AUTO_INCREMENT,UserUID int REFERENCES mUsers(UserUID), Password varchar(100),CreatedOn datetime);

ALTER TABLE `mUsers` ADD `VerificationStatus` TINYINT(1) NOT NULL DEFAULT '0' AFTER `Active`;
ALTER TABLE `mUsers` CHANGE `PasswordUpdatedDate` `PasswordUpdatedDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
UPDATE mUsers SET CreatedOn = '2019-01-01 00:00:00' WHERE CAST(CreatedOn AS CHAR(20)) = '0000-00-00 00:00:00'


UPDATE mUsers AS U1, mUsers AS U2 
SET U1.PasswordUpdatedDate = U2.CreatedOn
WHERE U2.UserUID = U1.UserUID;

ALTER TABLE `tOrderDocumentCheckIn` CHANGE `LoanType` `LoanType` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `tOrderDocumentCheckIn` CHANGE `LoanAmount` `LoanAmount` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;


-- New Column Docs Received for subqueues commit hash dcf98fc41e6c2f0fbdd169599c83e711fd7afbd4--
-- @author Praveen Kumar <praveen.kumar@avanzegroup.com>--
-- @since Friday 17 July 2020.--
ALTER TABLE `mQueues` ADD COLUMN `IsDocsReceived` tinyint(1) NULL DEFAULT 0 AFTER `BusinessHourEndTime`;
ALTER TABLE `tOrderQueues` ADD COLUMN `QueueIsDocsReceived` tinyint(1) NULL DEFAULT 0 AFTER `QueueStatus`;

ALTER TABLE `mQueueColumns` 
MODIFY COLUMN `WorkflowUID` smallint(4) NULL DEFAULT NULL AFTER `ColumnName`,
ADD COLUMN `QueueWorkflowUID` smallint(4) NULL DEFAULT NULL COMMENT 'Queue Workflow Column' AFTER `DocumentTypeUID`;


ALTER TABLE `mQueueColumns` 
ADD CONSTRAINT `DynamicWorkflowcolumns_fk` FOREIGN KEY (`WorkflowUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `DynamicQueueWorkflowColumns_fk` FOREIGN KEY (`WorkflowUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- Menu count subclass (side_new_icon_count added)  commit hash 01ef1f6442b48711fc1ba0e31e2afbc069f8cdfd--
-- @author Praveen Kumar <praveen.kumar@avanzegroup.com>--
-- @since Friday 17 July 2020.--
UPDATE mResources SET NotificationElement = '<span  class=\"badge badge-warning pull-right side_new_icon_count\" style=\"background-color: transparent;border: 1px solid #FFF;margin-top: 5pt;position: absolute;right: 0px;\"></span>' WHERE NotificationElement <> ''

-- mCustomerProduct Bulk Import format new type added commit hash 8ed1ea6e7808e7b958ccf7ec10b8e6aaf8f416f1--
-- @author Sathishkumar RKumar <sathish.kumar@avanzegroup.com>--
-- @since Friday 17 July 2020.--

ALTER TABLE `mCustomerProducts` CHANGE `BulkImportFormat` `BulkImportFormat` ENUM('Stacx-Standard','Stacx-Assignment','LOP-Standard','LOP-Assignment','NRZ-Bulk_Upload') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Stacx-Standard';

UPDATE `mCustomerProducts` SET `BulkImportFormat`="NRZ-Bulk_Upload",`BulkImportTemplateName`="NRZ-Assignment-BulkFormat.xlsx",`BulkImportTemplateXMLName`="DocTrac-Std-BulkFormat.xml" WHERE `CustomerUID`=28;


-- DOCS RECEIVED--
-- @author Praveen Kumar <praveen.kumar@avanzegroup.com>--
-- @since Friday 17 July 2020.--
ALTER TABLE `mQueues` ADD COLUMN `IsDocsReceived` tinyint(1) NULL DEFAULT 0 AFTER `BusinessHourEndTime`;
ALTER TABLE `tOrderQueues` ADD COLUMN `QueueIsDocsReceived` tinyint(1) NULL DEFAULT 0 AFTER `QueueStatus`;

ALTER TABLE `mQueueColumns` 
MODIFY COLUMN `WorkflowUID` smallint(4) NULL DEFAULT NULL AFTER `ColumnName`,
ADD COLUMN `QueueWorkflowUID` smallint(4) NULL DEFAULT NULL COMMENT 'Queue Workflow Column' AFTER `DocumentTypeUID`;



ALTER TABLE `mQueueColumns` 
ADD CONSTRAINT `DynamicWorkflowcolumns_fk` FOREIGN KEY (`WorkflowUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `DynamicQueueWorkflowColumns_fk` FOREIGN KEY (`WorkflowUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`) ON DELETE NO ACTION ON UPDATE NO ACTION;


-- DOCS OUT New Workflows DocsOut,SignedDocs & FundingConditions--
-- @author Praveen Kumar <praveen.kumar@avanzegroup.com>--
-- @since Friday 17 July 2020.--
INSERT INTO `mWorkFlowModules`(`WorkflowModuleUID`, `WorkflowModuleName`, `SystemName`, `WorkflowModuleDependentOn`, `WorkflowIcon`, `IsQuantifiable`, `Active`) VALUES (41, 'DocsOut', 'DocsOut', NULL, NULL, 0, 1);
INSERT INTO `mWorkFlowModules`(`WorkflowModuleUID`, `WorkflowModuleName`, `SystemName`, `WorkflowModuleDependentOn`, `WorkflowIcon`, `IsQuantifiable`, `Active`) VALUES (42, 'SignedDocs', 'SignedDocs', NULL, NULL, 0, 1);
INSERT INTO `mWorkFlowModules`(`WorkflowModuleUID`, `WorkflowModuleName`, `SystemName`, `WorkflowModuleDependentOn`, `WorkflowIcon`, `IsQuantifiable`, `Active`) VALUES (43, 'FundingConditions', 'FundingConditions', NULL, NULL, 0, 1);


-- DOCS OUT Resources--
-- @author Praveen Kumar <praveen.kumar@avanzegroup.com>--
-- @since Friday 17 July 2020.--
INSERT INTO `mResources`(`ResourceUID`, `CustomerUID`, `controller`, `FieldName`, `FieldSection`, `Parameters`, `NotificationElement`, `MenuBarType`, `IconClass`, `Position`, `Active`, `ParentType`, `WorkflowModuleUID`) VALUES ('', '29', 'DocsOut_Orders', 'Docs Out', 'WORKFLOW', '', '<span  class=\"badge badge-warning pull-right side_new_icon_count\" style=\"background-color: transparent;border: 1px solid #FFF;margin-top: 5pt;position: absolute;right: 0px;\"></span>', 'common', 'icon-file-check', 16, 1, '', 41);
INSERT INTO `mResources`(`ResourceUID`, `CustomerUID`, `controller`, `FieldName`, `FieldSection`, `Parameters`, `NotificationElement`, `MenuBarType`, `IconClass`, `Position`, `Active`, `ParentType`, `WorkflowModuleUID`) VALUES ('', '29', 'SignedDocs_Orders', 'Signed Docs', 'WORKFLOW', '', '<span  class=\"badge badge-warning pull-right side_new_icon_count\" style=\"background-color: transparent;border: 1px solid #FFF;margin-top: 5pt;position: absolute;right: 0px;\"></span>', 'common', 'icon-file-check', 17, 1, '', 42);
INSERT INTO `mResources`(`ResourceUID`, `CustomerUID`, `controller`, `FieldName`, `FieldSection`, `Parameters`, `NotificationElement`, `MenuBarType`, `IconClass`, `Position`, `Active`, `ParentType`, `WorkflowModuleUID`) VALUES ('', '29', 'FundingConditions_Orders', 'Funding Conditions', 'WORKFLOW', '', '<span  class=\"badge badge-warning pull-right side_new_icon_count\" style=\"background-color: transparent;border: 1px solid #FFF;margin-top: 5pt;position: absolute;right: 0px;\"></span>', 'common', 'icon-file-check', 18, 1, '', 43);

INSERT INTO `mResources`(`ResourceUID`, `CustomerUID`, `controller`, `FieldName`, `FieldSection`, `Parameters`, `NotificationElement`, `MenuBarType`, `IconClass`, `Position`, `Active`, `ParentType`, `WorkflowModuleUID`) VALUES ('', '29', 'Docs_Out', 'Docs Out', 'ORDERWORKFLOW', '', NULL, 'common', 'icon-file-check', 16, 1, '', 41);
INSERT INTO `mResources`(`ResourceUID`, `CustomerUID`, `controller`, `FieldName`, `FieldSection`, `Parameters`, `NotificationElement`, `MenuBarType`, `IconClass`, `Position`, `Active`, `ParentType`, `WorkflowModuleUID`) VALUES ('', '29', 'Signed_Docs', 'Signed Docs', 'ORDERWORKFLOW', '', NULL, 'common', 'icon-file-check', 17, 1, '', 42);
INSERT INTO `mResources`(`ResourceUID`, `CustomerUID`, `controller`, `FieldName`, `FieldSection`, `Parameters`, `NotificationElement`, `MenuBarType`, `IconClass`, `Position`, `Active`, `ParentType`, `WorkflowModuleUID`) VALUES ('', '29', 'Funding_Conditions', 'Funding Conditions', 'ORDERWORKFLOW', '', NULL, 'common', 'icon-file-check', 18, 1, '', 43);


-- DOCS OUT dynamic columns --
-- @author Praveen Kumar <praveen.kumar@avanzegroup.com>--
-- @since Friday 17 July 2020.--

INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Loan Number', 'tOrders.LoanNumber', 41, NULL, 29, 0, NULL, 0, NULL, 1);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Borrower Name', 'tOrderPropertyRole.BorrowerFirstName', 41, NULL, 29, 0, NULL, 0, NULL, 2);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'processor Name', 'tOrderImport.LoanProcessor', 41, NULL, 29, 0, NULL, 0, NULL, 3);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Milestone', 'mMilestone.MilestoneName', 41, NULL, 29, 0, NULL, 0, NULL, 4);

INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Loan Type', 'tOrders.LoanType', 41, NULL, 29, 0, NULL, 0, NULL, 3);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'State', 'tOrders.PropertyStateCode', 41, NULL, 29, 0, NULL, 0, NULL, 5);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Signing Date', 'tOrderImport.SigningDate', 41, NULL, 29, 0, NULL, 0, NULL, 6);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Signing Time', 'tOrderImport.SigningTime', 41, NULL, 29, 0, NULL, 0, NULL, 7);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Funder', 'tOrderImport.Funder', 41, NULL, 29, 0, NULL, 0, NULL, 8);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Closing Disclosure Send Date', 'tOrderImport.ClosingDisclosureSendDate', 41, NULL, 29, 0, NULL, 0, NULL, 9);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Earliest Closing Date', 'tOrderImport.EarliestClosingDate', 41, NULL, 29, 0, NULL, 0, NULL, 10);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Lock Expiration', 'tOrderImport.LockExpiration', 41, NULL, 29, 0, NULL, 0, NULL, 11);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Occupancy Status', 'tOrderImport.OccupancyStatus', 41, NULL, 29, 0, NULL, 0, NULL, 12);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Cash from borrower', 'tOrderImport.CashFromBorrower', 41, NULL, 29, 0, NULL, 0, NULL, 13);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'LC Required', 'LOGIC-LCREQUIRED', 41, NULL, 29, 0, NULL, 1, NULL, 14);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Queue', 'tOrderImport.Queue', 41, NULL, 29, 0, NULL, 0, NULL, 15);

	--signed queue--

	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Loan Number', 'tOrders.LoanNumber', 42, NULL, 29, 0, NULL, 0, NULL, 1);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Borrower Name', 'tOrderPropertyRole.BorrowerFirstName', 42, NULL, 29, 0, NULL, 0, NULL, 2);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'processor Name', 'tOrderImport.LoanProcessor', 42, NULL, 29, 0, NULL, 0, NULL, 3);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Milestone', 'mMilestone.MilestoneName', 42, NULL, 29, 0, NULL, 0, NULL, 4);

	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Loan Type', 'tOrders.LoanType', 42, NULL, 29, 0, NULL, 0, NULL, 3);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'State', 'tOrders.PropertyStateCode', 42, NULL, 29, 0, NULL, 0, NULL, 5);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Signing Date', 'tOrderImport.SigningDate', 42, NULL, 29, 0, NULL, 0, NULL, 6);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Signing Time', 'tOrderImport.SigningTime', 42, NULL, 29, 0, NULL, 0, NULL, 7);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Funder', 'tOrderImport.Funder', 42, NULL, 29, 0, NULL, 0, NULL, 8);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Closing Disclosure Send Date', 'tOrderImport.ClosingDisclosureSendDate', 42, NULL, 29, 0, NULL, 0, NULL, 9);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Earliest Closing Date', 'tOrderImport.EarliestClosingDate', 42, NULL, 29, 0, NULL, 0, NULL, 10);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Lock Expiration', 'tOrderImport.LockExpiration', 42, NULL, 29, 0, NULL, 0, NULL, 11);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Occupancy Status', 'tOrderImport.OccupancyStatus', 42, NULL, 29, 0, NULL, 0, NULL, 12);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Cash from borrower', 'tOrderImport.CashFromBorrower', 42, NULL, 29, 0, NULL, 0, NULL, 13);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'LC Required', 'LOGIC-LCREQUIRED', 42, NULL, 29, 0, NULL, 1, NULL, 14);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Queue', 'tOrderImport.Queue', 42, NULL, 29, 0, NULL, 0, NULL, 15);

	--funding conditions  --
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Loan Number', 'tOrders.LoanNumber', 43, NULL, 29, 0, NULL, 0, NULL, 1);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Borrower Name', 'tOrderPropertyRole.BorrowerFirstName', 43, NULL, 29, 0, NULL, 0, NULL, 2);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'processor Name', 'tOrderImport.LoanProcessor', 43, NULL, 29, 0, NULL, 0, NULL, 3);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Milestone', 'mMilestone.MilestoneName', 43, NULL, 29, 0, NULL, 0, NULL, 4);

	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Loan Type', 'tOrders.LoanType', 43, NULL, 29, 0, NULL, 0, NULL, 3);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'State', 'tOrders.PropertyStateCode', 43, NULL, 29, 0, NULL, 0, NULL, 5);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Signing Date', 'tOrderImport.SigningDate', 43, NULL, 29, 0, NULL, 0, NULL, 6);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Signing Time', 'tOrderImport.SigningTime', 43, NULL, 29, 0, NULL, 0, NULL, 7);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Funder', 'tOrderImport.Funder', 43, NULL, 29, 0, NULL, 0, NULL, 8);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Closing Disclosure Send Date', 'tOrderImport.ClosingDisclosureSendDate', 43, NULL, 29, 0, NULL, 0, NULL, 9);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Earliest Closing Date', 'tOrderImport.EarliestClosingDate', 43, NULL, 29, 0, NULL, 0, NULL, 10);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Lock Expiration', 'tOrderImport.LockExpiration', 43, NULL, 29, 0, NULL, 0, NULL, 11);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Occupancy Status', 'tOrderImport.OccupancyStatus', 43, NULL, 29, 0, NULL, 0, NULL, 12);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Cash from borrower', 'tOrderImport.CashFromBorrower', 43, NULL, 29, 0, NULL, 0, NULL, 13);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'LC Required', 'LOGIC-LCREQUIRED', 43, NULL, 29, 0, NULL, 1, NULL, 14);
	INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Queue', 'tOrderImport.Queue', 43, NULL, 29, 0, NULL, 0, NULL, 15);


--@vishnu Priya
ALTER TABLE `tDocumentCheckList` ADD `IsDelete` TINYINT(1) NOT NULL DEFAULT '1' AFTER `ModifiedDateTime`;
ALTER TABLE `tDocumentCheckList` ADD `Position` INT NULL AFTER `IsDelete`;


-- Queue dynamic columns in GateKeeping and submissions queue --
-- @author Praveen Kumar <praveen.kumar@avanzegroup.com>--
-- @since Friday 17 July 2020.--

INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'PreScreen', 'WorkflowQueue', 39, NULL, 29, 0, NULL, 1, 1, 'Queue', 14);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'HOI', 'WorkflowQueue', 39, NULL, 29, 0, NULL, 11, 1, 'Queue', 15);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Title', 'WorkflowQueue', 39, NULL, 29, 0, NULL, 3, 1, 'Queue', 16);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'FHA/VA', 'WorkflowQueue', 39, NULL, 29, 0, NULL, 4, 1, 'Queue', 17);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Workup', 'WorkflowQueue', 39, NULL, 29, 0, NULL, 7, 1, 'Queue', 18);



INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'PreScreen', 'WorkflowQueue', 40, NULL, 29, 0, NULL, 1, 1, 'Queue', 14);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'HOI', 'WorkflowQueue', 40, NULL, 29, 0, NULL, 11, 1, 'Queue', 15);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Title', 'WorkflowQueue', 40, NULL, 29, 0, NULL, 3, 1, 'Queue', 16);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'FHA/VA', 'WorkflowQueue', 40, NULL, 29, 0, NULL, 4, 1, 'Queue', 17);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Workup', 'WorkflowQueue', 40, NULL, 29, 0, NULL, 7, 1, 'Queue', 18);

-- Queue Workflow comments --
-- @author Praveen Kumar <praveen.kumar@avanzegroup.com>--
-- @since Wednesday 22 July 2020--

ALTER TABLE `tOrderComments` MODIFY COLUMN `WorkflowUID` smallint(4) NULL DEFAULT NULL AFTER `OrderUID`;

-- Queue dynamic columns in Workflow comments--
-- @author Praveen Kumar <praveen.kumar@avanzegroup.com>--
-- @since Thursday 23 July 2020--
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Comments', 'Workflow-Comments', 7, NULL, 29, 0, NULL, NULL, 1, NULL, 10);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Comments', 'Workflow-Comments', 1, NULL, 29, 0, NULL, NULL, 40, NULL, 10);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Comments', 'Workflow-Comments', 1, NULL, 29, 0, NULL, NULL, 39, NULL, 10);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Comments', 'Workflow-Comments', 1, NULL, 29, 0, NULL, NULL, 41, NULL, 10);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Comments', 'Workflow-Comments', 1, NULL, 29, 0, NULL, NULL, 42, NULL, 10);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Comments', 'Workflow-Comments', 1, NULL, 29, 0, NULL, NULL, 43, NULL, 10);

-- Queue Permissions for Queue--
-- @author Praveen Kumar <praveen.kumar@avanzegroup.com>--
-- @since Thursday 23 July 2020--
ALTER TABLE `mRole` ADD COLUMN `OrderQueue` tinyint(1) NULL DEFAULT 1 COMMENT '1-AllOrders,2-MyOrders' AFTER `IsAssigned`;

-- Highlight going to expire orders duration setup --
-- @author Sathishkumar <sathish.kumar@avanzegroup.com>--
-- @since Thursday 25 July 2020--
ALTER TABLE `mCustomerWorkflowModules` ADD `OrderHighlightDuration` INT(11) NULL AFTER `PropertyType`;
ALTER TABLE `mCustomerWorkflowModules` CHANGE `OrderHighlightDuration` `OrderHighlightDuration` VARCHAR(10) NULL DEFAULT NULL;

-- NewRez All the workflows to have comment section for each loan --
-- @author Sathishkumar <sathish.kumar@avanzegroup.com>--
-- @since Friday 31 July 2020--
INSERT INTO `mQueueColumns` (`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES 
(NULL, 'Comments', 'Workflow-Comments', '1', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '2', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '12', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '17', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '18', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '19', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '11', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '20', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '37', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '21', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '22', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '23', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '24', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '38', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '25', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '26', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '27', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '28', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '29', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '30', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '31', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '32', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '33', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '34', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '35', NULL, '28', '0', NULL, NULL, '1', NULL, '10'),
(NULL, 'Comments', 'Workflow-Comments', '36', NULL, '28', '0', NULL, NULL, '1', NULL, '10');

/**
*Function Bulk Assign 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Friday 31 July 2020.
*/
ALTER TABLE `mCustomerProducts` ADD `BulkAssignFormat` ENUM('NRZ-Assign','Cooper-Assign') NULL;
ALTER TABLE `mCustomerProducts` ADD `BulkAssignTemplateName` VARCHAR(100) NULL AFTER `BulkAssignFormat`;
UPDATE `mCustomerProducts` SET `BulkAssignFormat` = 'NRZ-Assign', `BulkAssignTemplateName` = 'DocTrac-NRZ-Bulk-Assign-Format.xlsx' WHERE `mCustomerProducts`.`ProductUID` = 13;


--workup associate and completed datetime in cd queue

INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Workup Associate', 'WorkflowCompletedAssociate', 15, NULL, 29, 0, NULL, 7, 1, 'tOrders.OrderEntryDateTime', 8);
INSERT INTO `mQueueColumns`(`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES ('', 'Workup CompletedDate', 'WorkflowCompletedDate', 15, NULL, 29, 0, NULL, 7, 1, 'tOrders.OrderEntryDateTime', 8);


--When reversing order, include Remarks options need to show new column in kickback

ALTER TABLE `tOrderWorkflows` ADD COLUMN `ReversedRemarks` varchar(100) NULL AFTER `IsReversed`;

--Queue Status SubQueues
ALTER TABLE `mQueues` ADD COLUMN `IsStatus` tinyint(1) NULL DEFAULT 0 AFTER `IsDocsReceived`;

ALTER TABLE `tOrderQueues` ADD COLUMN `QueueIsStatus` enum('','Requested','Approved','Denied','Sales Restructure','Not Required') NULL DEFAULT NULL AFTER `QueueIsDocsReceived`;

--Reversed user and datetime --
-- @author Praveen Kumar <praveen.kumar@avanzegroup.com>--
-- @since Monday 10 August 2020--
ALTER TABLE `tOrderWorkflows` ADD COLUMN `ReversedByUserUID` int NULL DEFAULT NULL AFTER `IsReversed`;
ALTER TABLE `tOrderWorkflows` ADD COLUMN `ReversedDateTime` datetime NULL DEFAULT NULL AFTER `ReversedByUserUID`;

/**
*Description Create tDocumentChecklistHistory table structure 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Tuesday 11 August 2020.
*/
CREATE TABLE `tDocumentCheckListHistory` (
  `OrderUID` int(11) NOT NULL,
  `CategoryUID` int(11) DEFAULT NULL,
  `DocumentTypeUID` int(11) DEFAULT NULL,
  `DocumentTypeName` varchar(300) DEFAULT NULL,
  `Answer` enum('Completed','Problem Identified','NA') NOT NULL,
  `Comments` longtext DEFAULT NULL,
  `IsChaseSend` enum('NA','YES','CANCELLED','COMPLETED') NOT NULL,
  `WorkflowUID` int(11) DEFAULT NULL,
  `FileUploaded` enum('Yes','No') NOT NULL,
  `DocumentDate` varchar(100) DEFAULT NULL,
  `DocumentType` varchar(100) DEFAULT NULL,
  `DocumentExpiryDate` varchar(100) DEFAULT NULL,
  `ModifiedUserUID` int(11) DEFAULT NULL,
  `ModifiedDateTime` datetime NOT NULL,
  `IsDelete` tinyint(1) NOT NULL DEFAULT 1,
  `Position` int(11) DEFAULT NULL,
  `CreatedDateTime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;




ALTER TABLE `mCustomer` ADD `ReportDateSelection` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '0=>Order Entry Datetime,1=>Inflow datetime' AFTER `HighlightExpiryOrders`;
ALTER TABLE `tOrderImport` ADD COLUMN `InflowDate` varchar(100) NULL DEFAULT NULL AFTER `Queue`;

/**
*Agent level restrict self assign 
*Lock Expiration Restriction
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Friday 14 August 2020.
*/
ALTER TABLE `mRole` ADD `IsSelfAssignEnabled` TINYINT(1) NULL DEFAULT '0' AFTER `IsReverseEnabled`;
ALTER TABLE `mRole` ADD `IsLockExpirationRestricted` TINYINT(1) NULL DEFAULT '0' AFTER `IsSelfAssignEnabled`;
/**
*Agent level Get Next Order Assign button 
*@author harini <harini.bnagari@avanzegroup.com>
*@since Tuesday 18 August 2020.
*/

ALTER TABLE `mRole` ADD `AssignGetNextOrder` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'For Get Next orders button' AFTER `IsLockExpirationRestricted`;

/**
*Processor role type created 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Wednesday 19 August 2020.
*/
INSERT INTO `mRoleType` (`RoleTypeUID`, `RoleTypeName`, `Active`) VALUES ('10', 'Processor', '1');

/**
*Processor Junior proccessor group master table 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Wednesday 19 August 2020.
*/	
CREATE TABLE `mJuniorProcessorGroup` (
 `GroupUID` int(11) NOT NULL AUTO_INCREMENT,
 `GroupCustomerUID` int(11) NOT NULL,
 `JuniorProcessorUserUID` int(11) NOT NULL,
 `ProcessorUserUIDs` varchar(500) DEFAULT NULL,
 `Active` tinyint(1) DEFAULT 1,
 `CreatedByUserUID` int(11) NOT NULL,
 `CreatedOn` datetime NOT NULL DEFAULT current_timestamp(),
 `ModifiedByUserUID` int(11) DEFAULT NULL,
 `ModifiedOn` datetime NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`GroupUID`),
 KEY `JuniorProcessorUserUID` (`JuniorProcessorUserUID`),
 KEY `GroupCustomerUID` (`GroupCustomerUID`),
 KEY `CreatedByUserUID` (`CreatedByUserUID`),
 KEY `ModifiedByUserUID` (`ModifiedByUserUID`),
 CONSTRAINT `mjuniorprocessorgroup_ibfk_1` FOREIGN KEY (`JuniorProcessorUserUID`) REFERENCES `mUsers` (`UserUID`),
 CONSTRAINT `mjuniorprocessorgroup_ibfk_2` FOREIGN KEY (`GroupCustomerUID`) REFERENCES `mCustomer` (`CustomerUID`),
 CONSTRAINT `mjuniorprocessorgroup_ibfk_3` FOREIGN KEY (`CreatedByUserUID`) REFERENCES `mUsers` (`UserUID`),
 CONSTRAINT `mjuniorprocessorgroup_ibfk_4` FOREIGN KEY (`ModifiedByUserUID`) REFERENCES `mUsers` (`UserUID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4

/**
*tOrderImport Marital Status Column Added` 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Friday 21 August 2020.
*/
ALTER TABLE `tOrderImport` ADD `MaritalStatus` VARCHAR(100) NULL AFTER `InflowDate`;

/**
*Highlight lock expiry order column based on logic 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Friday 21 August 2020.
*/
ALTER TABLE `mCustomer` ADD `HighlightLockExpiryOrdersColumn` TINYINT(1) NULL AFTER `HighlightExpiryOrders`;

/**
*Function Junior Processor Report tables 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Date.
*/
ALTER TABLE `mJuniorProcessorGroup` DROP `ProcessorUserUIDs`;

CREATE TABLE `mJuniorProcessorUsers` (
 `GroupUID` int(11) NOT NULL,
 `ProcessorUserUID` int(11) NOT NULL,
 KEY `GroupUID` (`GroupUID`),
 KEY `ProcessorUserUID` (`ProcessorUserUID`),
 CONSTRAINT `mjuniorprocessorusers_ibfk_1` FOREIGN KEY (`GroupUID`) REFERENCES `mJuniorProcessorGroup` (`GroupUID`),
 CONSTRAINT `mjuniorprocessorusers_ibfk_2` FOREIGN KEY (`ProcessorUserUID`) REFERENCES `mUsers` (`UserUID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `mJuniorProcessorWorkflows` (
 `GroupUID` int(11) NOT NULL,
 `WorkflowModuleUID` smallint(4) NOT NULL,
 `QueueUID` int(11) NOT NULL,
 KEY `GroupUID` (`GroupUID`),
 KEY `WorkflowModuleUID` (`WorkflowModuleUID`),
 KEY `QueueUID` (`QueueUID`),
 CONSTRAINT `mjuniorprocessorworkflows_ibfk_1` FOREIGN KEY (`GroupUID`) REFERENCES `mJuniorProcessorGroup` (`GroupUID`),
 CONSTRAINT `mjuniorprocessorworkflows_ibfk_2` FOREIGN KEY (`QueueUID`) REFERENCES `mQueues` (`QueueUID`),
 CONSTRAINT `mjuniorprocessorworkflows_ibfk_3` FOREIGN KEY (`WorkflowModuleUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `mJuniorProcessorWorkflows` CHANGE `QueueUID` `QueueUID` INT(11) NULL;

ALTER TABLE `mJuniorProcessorWorkflows` ADD `IsKickBack` TINYINT(1) NULL AFTER `QueueUID`;

ALTER TABLE `mQueueColumns` 
ADD COLUMN `FieldUID` int NULL DEFAULT NULL AFTER `WorkflowUID`;

ALTER TABLE `tOrderImport` 
ADD COLUMN `OrderJuniorProcessorComments` varchar(255) NULL DEFAULT NULL;

/**
*Function Order esclation table 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Thursday 27 August 2020.
*/
CREATE TABLE `tOrderReWork` (
 `OrderUID` int(11) NOT NULL,
 `WorkflowModuleUID` smallint(4) NOT NULL,
 `IsReWorkEnabled` tinyint(1) NOT NULL,
 `EnabledByUserUID` int(11) NOT NULL,
 `EnabledDateTime` datetime NOT NULL,
 `CompletedByUserUID` int(11) DEFAULT NULL,
 `CompletedDateTime` datetime DEFAULT NULL,
 KEY `OrderUID` (`OrderUID`),
 KEY `WorkflowModuleUID` (`WorkflowModuleUID`),
 KEY `EnabledByUserUID` (`EnabledByUserUID`),
 KEY `CompletedByUserUID` (`CompletedByUserUID`),
 CONSTRAINT `torderrework_ibfk_1` FOREIGN KEY (`OrderUID`) REFERENCES `tOrders` (`OrderUID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
 CONSTRAINT `torderrework_ibfk_2` FOREIGN KEY (`WorkflowModuleUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
 CONSTRAINT `torderrework_ibfk_3` FOREIGN KEY (`EnabledByUserUID`) REFERENCES `mUsers` (`UserUID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
 CONSTRAINT `torderrework_ibfk_4` FOREIGN KEY (`CompletedByUserUID`) REFERENCES `mUsers` (`UserUID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `tOrderReWork` ADD `OrderReWorkUID` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`OrderReWorkUID`);

-- dynamic column query for Gatekeeping rework completedby and completeddatetime

INSERT INTO `mQueueColumns` (`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `FieldUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES
(NULL, 'Re-Work Completed By', 'ReWorkCompletedBy', 39, NULL, NULL, 29, 0, NULL, NULL, 1, 'ReWorkCompletedBy', 19),
(NULL, 'Re-Work Completed Date and Time', 'ReWorkCompletedDateTime', 39, NULL, NULL, 29, 0, NULL, NULL, 1, 'ReWorkCompletedDateTime', 20);

/**
*tOrderWorkflowsData table 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Friday 04 September 2020.
*/
CREATE TABLE `tOrderWorkflowsData` (
 `OrderUID` int(11) NOT NULL,
 `WorkflowModuleUID` smallint(4) NOT NULL,
 `IsPhoneEnabled` tinyint(1) DEFAULT NULL,
 `IsEmailEnabled` tinyint(1) DEFAULT NULL,
 KEY `OrderUID` (`OrderUID`),
 KEY `WorkflowModuleUID` (`WorkflowModuleUID`),
 CONSTRAINT `torderworkflowsdata_ibfk_1` FOREIGN KEY (`OrderUID`) REFERENCES `tOrders` (`OrderUID`),
 CONSTRAINT `torderworkflowsdata_ibfk_2` FOREIGN KEY (`WorkflowModuleUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/**
*PayyOffUpdate
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Friday 11 September 2020.
*/
ALTER TABLE `mCustomerProducts` ADD `PayOffBulkUpdateFormat` ENUM('Cooper-PayOffBulkUpdate') NULL AFTER `BulkAssignTemplateName`;
ALTER TABLE `mCustomerProducts` ADD `PayOffBulkUpdateTemplateName` VARCHAR(100) NULL AFTER `PayOffBulkUpdateFormat`;
UPDATE `mCustomerProducts` SET `PayOffBulkUpdateFormat` = 'Cooper-PayOffBulkUpdate' WHERE `mCustomerProducts`.`ProductUID` = 11;
UPDATE `mCustomerProducts` SET `PayOffBulkUpdateTemplateName` = 'Doctrac-PayOffUpdate-BulkFormat.xlsx' WHERE `mCustomerProducts`.`ProductUID` = 11;
ALTER TABLE `tOrderImport` ADD `LOName` VARCHAR(300) NULL AFTER `OrderJuniorProcessorComments`;
ALTER TABLE `tOrderImport` ADD `BorrowerName` VARCHAR(100) NULL AFTER `LOName`;
ALTER TABLE `tOrderImport` ADD `LastPaymentReceivedDate` VARCHAR(100) NULL AFTER `BorrowerEmail`;
INSERT INTO `mQueueColumns` (`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `FieldUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `Position`) VALUES
(NULL, 'Last Payment Received Date', 'tOrderImport.LastPaymentReceivedDate', NULL, NULL, 'PayOffOrders', 29, NULL, NULL, NULL, 0, '', 7),
(NULL, 'Borrower Email', 'tOrderImport.BwrEmail', NULL, NULL, 'PayOffOrders', 29, NULL, NULL, NULL, 0, '', 6),
(NULL, 'Borrower Name', 'tOrderImport.BorrowerName', NULL, NULL, 'PayOffOrders', 29, NULL, NULL, NULL, 0, '', 5),
(NULL, 'State', 'tOrders.PropertyStateCode', NULL, NULL, 'PayOffOrders', 29, NULL, NULL, NULL, 0, '', 4),
(NULL, 'LO Name', 'tOrderImport.LOName', NULL, NULL, 'PayOffOrders', 29, NULL, NULL, NULL, 0, '', 3),
(NULL, 'Processor Name', 'tOrderImport.LoanProcessor', NULL, NULL, 'PayOffOrders', 29, NULL, NULL, NULL, 0, '', 2),
(NULL, 'Loan Number', 'tOrders.LoanNumber', NULL, NULL, 'PayOffOrders', 29, NULL, NULL, NULL, 0, '', 1);

/**
*Cooper bulk assign template
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Monday 14 September 2020.
*/
UPDATE `mCustomerProducts` SET `BulkAssignFormat` = 'Cooper-Assign', `BulkAssignTemplateName` = 'DocTrac-Cooper-Bulk-Assign-Format.xlsx' WHERE `mCustomerProducts`.`ProductUID` = 11;

/**
*notification enabled queues table
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Friday 18 September 2020.
*/
CREATE TABLE `mQueuesNotification` (
 `WorkflowModuleUID` smallint(4) NOT NULL,
 `QueueUID` int(11) NOT NULL,
 KEY `WorkflowModuleUID` (`WorkflowModuleUID`),
 KEY `QueueUID` (`QueueUID`),
 CONSTRAINT `mqueuesnotification_ibfk_1` FOREIGN KEY (`QueueUID`) REFERENCES `mQueues` (`QueueUID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
 CONSTRAINT `mqueuesnotification_ibfk_2` FOREIGN KEY (`WorkflowModuleUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4

ALTER TABLE `tOrderWorkflows` ADD `IsCountable` TINYINT NOT NULL DEFAULT '0' AFTER `IsForceEnabled`;

ALTER TABLE `tOrderWorkflows` CHANGE `IsCountable` `IsCountDisabled` TINYINT(1) NULL DEFAULT NULL;

-- Order Reverse History table added
CREATE TABLE `tOrderReverse` (
 `OrderReverseUID` int(11) NOT NULL AUTO_INCREMENT,
 `OrderUID` int(11) NOT NULL,
 `WorkflowModuleUID` smallint(4) NOT NULL,
 `ReversedByUserUID` int(11) DEFAULT NULL,
 `ReversedDateTime` datetime DEFAULT NULL,
 `ReversedRemarks` varchar(100) DEFAULT NULL,
 PRIMARY KEY (`OrderReverseUID`),
 KEY `OrderUID` (`OrderUID`),
 KEY `ReversedByUserUID` (`ReversedByUserUID`),
 CONSTRAINT `torderreverse_ibfk_1` FOREIGN KEY (`OrderUID`) REFERENCES `tOrders` (`OrderUID`),
 CONSTRAINT `torderreverse_ibfk_2` FOREIGN KEY (`ReversedByUserUID`) REFERENCES `mUsers` (`UserUID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- Bulk Workflow Enable Alter Query
ALTER TABLE `mCustomerProducts` ADD `BulkWorkflowEnableFormat` ENUM('Cooper-BulkWorkflowEnable') NULL DEFAULT NULL AFTER `PayOffBulkUpdateTemplateName`, ADD `BulkWorkflowEnableTemplateName` VARCHAR(100) NULL DEFAULT NULL AFTER `BulkWorkflowEnableFormat`;
UPDATE `mCustomerProducts` SET `BulkWorkflowEnableFormat` = 'Cooper-BulkWorkflowEnable', `BulkWorkflowEnableTemplateName` = ' \r\nDoctrac-Cooper-BulkWorkflowEnable-Format.xlsx' WHERE `mCustomerProducts`.`ProductUID` = 11;

ALTER TABLE `mLoanType` ADD `LockExpiration` VARCHAR(100) NULL DEFAULT NULL AFTER `ClientUID`;

-- PayOff Date
ALTER TABLE `mCustomer` ADD `PayOff_Date` VARCHAR(100) NULL DEFAULT NULL AFTER `ReportDateSelection`;

-- Workflow Documents Table
CREATE TABLE `mWorkflowDocuments` (
 `DocumentUID` int(11) NOT NULL AUTO_INCREMENT,
 `WorkflowModuleUID` smallint(4) NOT NULL,
 `DocumentName` varchar(300) NOT NULL,
 `DocumentURL` varchar(300) NOT NULL,
 `UploadedByUserUID` int(11) NOT NULL,
 `UploadedDateTime` datetime NOT NULL,
 PRIMARY KEY (`DocumentUID`),
 KEY `UploadedByUserUID` (`UploadedByUserUID`),
 KEY `WorkflowModuleUID` (`WorkflowModuleUID`),
 CONSTRAINT `mworkflowdocuments_ibfk_1` FOREIGN KEY (`UploadedByUserUID`) REFERENCES `mUsers` (`UserUID`),
 CONSTRAINT `mworkflowdocuments_ibfk_2` FOREIGN KEY (`WorkflowModuleUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

-- HOI Remarks
ALTER TABLE `tOrderReWork` ADD `EnabledRemarks` VARCHAR(300) NULL DEFAULT NULL AFTER `EnabledDateTime`;
ALTER TABLE `tOrderReWork` ADD `CompletedRemarks` VARCHAR(300) NULL DEFAULT NULL AFTER `CompletedDateTime`;

-- Categories
ALTER TABLE `mQueueColumns` ADD `SubQueueCategoryUID` INT(11) NULL DEFAULT NULL AFTER `SortColumnName`;

CREATE TABLE `mCategories` (
 `CategoryUID` int(11) NOT NULL AUTO_INCREMENT,
 `CategoryName` varchar(300) NOT NULL,
 PRIMARY KEY (`CategoryUID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4

CREATE TABLE `mSubQueueCategory` (
 `SubQueueCategoryUID` int(11) NOT NULL AUTO_INCREMENT,
 `WorkflowModuleUID` smallint(4) NOT NULL,
 `SubQueueUID` int(11) DEFAULT NULL,
 `Section` enum('tblNewOrders','workingprogresstable','myorderstable','parkingorderstable','completedorderstable','Expiredorderstable','KickBackorderstable') DEFAULT NULL,
 `CategoryUIDs` varchar(300) NOT NULL,
 PRIMARY KEY (`SubQueueCategoryUID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4

CREATE TABLE `tSubQueueCategory` (
 `OrderUID` int(11) NOT NULL,
 `SubQueueCategoryUID` int(11) NOT NULL,
 `CategoryUID` int(11) NOT NULL,
 `LastModifiedByUserUID` int(11) DEFAULT NULL,
 `LastModifiedDateTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4

ALTER TABLE `mSubQueueCategory` CHANGE `Section` `SubQueueSection` ENUM('tblNewOrders','workingprogresstable','myorderstable','parkingorderstable','completedorderstable','Expiredorderstable','KickBackorderstable') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

ALTER TABLE `mSubQueueCategory` CHANGE `SubQueueSection` `SubQueueSection` ENUM('tblNewOrders','workingprogresstable','myorderstable','parkingorderstable','completedorderstable','Expiredorderstable','KickBackorderstable','hoiwaitingorderstable','hoiresponsedorderstable','hoireceivedorderstable','hoiexceptionorderstable','HOIReworkOrdersTable') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

-- Order Entry Template Fields
ALTER TABLE `tOrderImport` ADD `ZipCode` VARCHAR(9) NULL DEFAULT NULL AFTER `PropertyZipCode`, ADD `DOB` VARCHAR(45) NULL DEFAULT NULL AFTER `ZipCode`;

ALTER TABLE `tOrderImport` ADD `Term` VARCHAR(300) NULL DEFAULT NULL AFTER `DOB`, ADD `Product` VARCHAR(300) NULL DEFAULT NULL AFTER `Term`;

--- checklist changes

ALTER TABLE `mDocumentType` 
ADD COLUMN `HeadingName` varchar(100) NULL DEFAULT NULL AFTER `DocumentTypeName`;

ALTER TABLE `mDocumentType` 
ADD COLUMN `GroupHeadingName` varchar(100) NULL DEFAULT NULL AFTER `HeadingName`;

ALTER TABLE `mDocumentType` 
MODIFY COLUMN `FieldType` varchar(55) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL AFTER `EmailTemplate`;

ALTER TABLE `mDocumentType` 
MODIFY COLUMN `ExcludeExport` tinyint(1) NULL DEFAULT 0 AFTER `HashCode`,
MODIFY COLUMN `ToMails` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL AFTER `ExcludeExport`,
MODIFY COLUMN `EmailTemplate` int NULL AFTER `ToMails`;

-- Checklist findings yes option alter query
ALTER TABLE `tDocumentCheckList` CHANGE `Answer` `Answer` ENUM('Completed','Problem Identified','NA','Yes') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

-- Dynamic Column Issue Checklist Mapping
ALTER TABLE `mQueueColumns` ADD `ChecklistIssueWorkflowUID` SMALLINT(4) NULL DEFAULT NULL AFTER `SubQueueCategoryUID`, ADD `ChecklistIssueSubQueueUID` INT(11) NULL DEFAULT NULL AFTER `ChecklistIssueWorkflowUID`;

ALTER TABLE `tOrderImport` 
CHANGE COLUMN `Product` `NoteRate` varchar(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `Term`;

-- Workup CD section
CREATE TABLE `tOrderSubQueues` (
 `OrderSubQueueUID` int(11) NOT NULL AUTO_INCREMENT,
 `OrderUID` int(11) NOT NULL,
 `WorkflowModuleUID` smallint(4) NOT NULL,
 `SubQueueStatus` enum('Pending','Completed') NOT NULL DEFAULT 'Pending',
 `RaisedByUserUID` int(11) NOT NULL,
 `RaisedDateTime` datetime NOT NULL,
 `CompletedByUserUID` int(11) DEFAULT NULL,
 `CompletedDateTime` datetime DEFAULT NULL,
 PRIMARY KEY (`OrderSubQueueUID`),
 KEY `OrderUID` (`OrderUID`),
 KEY `WorkflowModuleUID` (`WorkflowModuleUID`),
 KEY `RaisedByUserUID` (`RaisedByUserUID`),
 KEY `CompletedByUserUID` (`CompletedByUserUID`),
 CONSTRAINT `tordersubqueues_ibfk_1` FOREIGN KEY (`OrderUID`) REFERENCES `tOrders` (`OrderUID`),
 CONSTRAINT `tordersubqueues_ibfk_2` FOREIGN KEY (`WorkflowModuleUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`),
 CONSTRAINT `tordersubqueues_ibfk_3` FOREIGN KEY (`RaisedByUserUID`) REFERENCES `mUsers` (`UserUID`),
 CONSTRAINT `tordersubqueues_ibfk_4` FOREIGN KEY (`CompletedByUserUID`) REFERENCES `mUsers` (`UserUID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- Expiry order complete
CREATE TABLE `tOrderChecklistExpiryComplete` (
 `ChecklistExpiryCompleteUID` int(11) NOT NULL AUTO_INCREMENT,
 `OrderUID` int(11) NOT NULL,
 `WorkflowModuleUID` smallint(4) NOT NULL,
 `CompletedByUserUID` int(11) DEFAULT NULL,
 `CompletedDateTime` datetime DEFAULT NULL,
 PRIMARY KEY (`ChecklistExpiryCompleteUID`),
 KEY `OrderUID` (`OrderUID`),
 KEY `WorkflowModuleUID` (`WorkflowModuleUID`),
 KEY `CompletedByUserUID` (`CompletedByUserUID`),
 CONSTRAINT `torderchecklistexpirycomplete_ibfk_1` FOREIGN KEY (`OrderUID`) REFERENCES `tOrders` (`OrderUID`),
 CONSTRAINT `torderchecklistexpirycomplete_ibfk_2` FOREIGN KEY (`WorkflowModuleUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`),
 CONSTRAINT `torderchecklistexpirycomplete_ibfk_3` FOREIGN KEY (`CompletedByUserUID`) REFERENCES `mUsers` (`UserUID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- Reverse Initiated WorkflowModuleUID 
ALTER TABLE `tOrderReverse` ADD `ReverseInitiatedWorkflowModuleUID` SMALLINT(4) NULL DEFAULT NULL AFTER `WorkflowModuleUID`;
ALTER TABLE `tOrderReverse` ADD CONSTRAINT `ReverseInitiatedWorkflowModuleUID` FOREIGN KEY (`ReverseInitiatedWorkflowModuleUID`) REFERENCES `mWorkFlowModules` (`WorkflowModuleUID`) ON DELETE RESTRICT ON UPDATE RESTRICT;
INSERT INTO `mQueueColumns` (`QueueColumnUID`, `HeaderName`, `ColumnName`, `WorkflowUID`, `FieldUID`, `Section`, `CustomerUID`, `IsChecklist`, `DocumentTypeUID`, `QueueWorkflowUID`, `NoSort`, `SortColumnName`, `SubQueueCategoryUID`, `ChecklistIssueWorkflowUID`, `ChecklistIssueSubQueueUID`, `Position`) VALUES
(NULL, 'Reversed Workflows', 'ReversedWorkflows', 39, NULL, NULL, 29, 0, NULL, NULL, 1, 'ReversedWorkflows', NULL, NULL, NULL, 21);

-- Checklist Group Headings and their issues count to be showed as column
ALTER TABLE `mQueueColumns` ADD `DocumentTypeUIDs` VARCHAR(255) NULL DEFAULT NULL AFTER `DocumentTypeUID`;

-- Multiple reasons for Subqueue initiation and completion
ALTER TABLE `tOrderQueues` CHANGE `RaisedReasonUID` `RaisedReasonUID` VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE `tOrderQueues` CHANGE `CompletedReasonUID` `CompletedReasonUID` VARCHAR(50) NULL DEFAULT NULL;

-- Subqueue Aging
ALTER TABLE `mQueueColumns` ADD `SubQueueAging` ENUM('Calendar Days','Business Days') NULL DEFAULT NULL AFTER `ChecklistIssueSubQueueUID`;
ALTER TABLE `mQueueColumns` ADD `StaticQueueUIDs` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Enable the column for static queue with a value separated by a comma' AFTER `ChecklistIssueSubQueueUID`;
ALTER TABLE `mQueueColumns` ADD `QueueUIDs` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Enable the column for dynamic queue with a value separated by a comma' AFTER `StaticQueueUIDs`;

-- PayOff Update
ALTER TABLE `mCustomerProducts` ADD `DocsOutBulkUpdateFormat` ENUM('Cooper-DocsOut-Update') NULL DEFAULT NULL AFTER `BulkWorkflowEnableTemplateName`, ADD `DocsOutBulkUpdateTemplateName` VARCHAR(100) NULL DEFAULT NULL AFTER `DocsOutBulkUpdateFormat`;
UPDATE `mCustomerProducts` SET `DocsOutBulkUpdateFormat` = 'Cooper-DocsOut-Update' WHERE `mCustomerProducts`.`ProductUID` = 11;
UPDATE `mCustomerProducts` SET `DocsOutBulkUpdateTemplateName` = 'DocTrac-DocsOut-Update-BulkFormat.xlsx' WHERE `mCustomerProducts`.`ProductUID` = 11;

-- queue date & time
ALTER TABLE `mSubQueueCategory` CHANGE `SubQueueSection` `SubQueueSection` ENUM('tblNewOrders','workingprogresstable','myorderstable','parkingorderstable','completedorderstable','Expiredorderstable','KickBackorderstable','hoiwaitingorderstable','hoiresponsedorderstable','hoireceivedorderstable','hoiexceptionorderstable','HOIReworkOrdersTable','DocsCheckedorderstable') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

ALTER TABLE `tOrderImport` DROP `QueueDateTime`;
ALTER TABLE `tOrderImport` ADD `QueueDateTime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `NoteRate`;

-- Docs Out Bulk Update Columns
ALTER TABLE `tOrderImport` ADD `DocsOutSigningDate` VARCHAR(100) NULL DEFAULT NULL AFTER `QueueDateTime`, ADD `DocsOutSigningTime` VARCHAR(100) NULL DEFAULT NULL AFTER `DocsOutSigningDate`, ADD `DocsOutClosingDisclosureSendDate` VARCHAR(100) NULL DEFAULT NULL AFTER `DocsOutSigningTime`;

ALTER TABLE `tOrderImport`  ADD `SigningStatusDate` VARCHAR(100) NULL DEFAULT NULL  AFTER `DocsOutClosingDisclosureSendDate`,  ADD `FundingFunderName` VARCHAR(300) NULL DEFAULT NULL  AFTER `SigningStatusDate`,  ADD `SigningLocation` VARCHAR(100) NULL DEFAULT NULL  AFTER `FundingFunderName`,  ADD `Credit3Amt` VARCHAR(100) NULL DEFAULT NULL  AFTER `SigningLocation`,  ADD `Credit4Amt` VARCHAR(100) NULL DEFAULT NULL  AFTER `Credit3Amt`,  ADD `ExceptionAmount` VARCHAR(100) NULL DEFAULT NULL  AFTER `Credit4Amt`,  ADD `Status` VARCHAR(100) NULL DEFAULT NULL  AFTER `ExceptionAmount`,  ADD `SubStatus` VARCHAR(100) NULL DEFAULT NULL  AFTER `Status`,  ADD `OccupancyPSI` VARCHAR(100) NULL DEFAULT NULL  AFTER `SubStatus`,  ADD `UnderwritingApprovalDate` VARCHAR(100) NULL DEFAULT NULL  AFTER `OccupancyPSI`,  ADD `AlertRedisclosureRequired` VARCHAR(100) NULL DEFAULT NULL  AFTER `UnderwritingApprovalDate`,  ADD `CountSubmittedforDocCheckQueueresubmissions` VARCHAR(100) NULL DEFAULT NULL  AFTER `AlertRedisclosureRequired`,  ADD `CDCallOutDate` VARCHAR(100) NULL DEFAULT NULL  AFTER `CountSubmittedforDocCheckQueueresubmissions`,  ADD `ApprovalMilestoneCount` VARCHAR(100) NULL DEFAULT NULL  AFTER `CDCallOutDate`,  ADD `ApprovedMilestoneDate` VARCHAR(100) NULL DEFAULT NULL  AFTER `ApprovalMilestoneCount`,  ADD `LastFinishedMilestone` VARCHAR(100) NULL DEFAULT NULL  AFTER `ApprovedMilestoneDate`;

INSERT INTO `mStaticQueues` (`StaticQueueUID`, `StaticQueueTableName`, `ClientUID`, `Active`) VALUES (NULL, 'DocsCheckedorderstable', '29', '1');

INSERT INTO `mCategories` (`CategoryUID`, `CategoryName`) VALUES (NULL, 'STC issue'), (NULL, 'Expired Doc Alert'), (NULL, 'Updated Taxcert'), (NULL, 'HOI renewal'), (NULL, 'Updated Payoff'), (NULL, 'LC or PBR'), (NULL, 'Encompass Alerts'), (NULL, 'MSS Requested'), (NULL, 'Final Approval group validation'), (NULL, 'Underwriter conditions'), (NULL, 'Mavent Fail or Alerts'), (NULL, 'Vesting & NBS issue');

ALTER TABLE `tOrderImport` CHANGE `QueueDateTime` `QueueDateTime` VARCHAR(100) NULL DEFAULT NULL;

INSERT INTO `mStaticQueues` (`StaticQueueUID`, `StaticQueueTableName`, `ClientUID`, `Active`) VALUES (NULL, 'PendiingUWorderstable', '29', '1'), (NULL, 'SubmittedforDocCheckOrdersTable', '29', '1');

ALTER TABLE `mSubQueueCategory` CHANGE `SubQueueSection` `SubQueueSection` ENUM('tblNewOrders','workingprogresstable','myorderstable','parkingorderstable','completedorderstable','Expiredorderstable','KickBackorderstable','hoiwaitingorderstable','hoiresponsedorderstable','hoireceivedorderstable','hoiexceptionorderstable','HOIReworkOrdersTable','DocsCheckedorderstable','PendiingUWorderstable','SubmittedforDocCheckOrdersTable') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

-- Static queue followup alter query
ALTER TABLE `tOrderFollowUp` ADD `StaticQueueUID` INT(11) NULL DEFAULT NULL AFTER `QueueUID`;
ALTER TABLE `tOrderFollowUp` ADD INDEX(`StaticQueueUID`);
ALTER TABLE `tOrderFollowUp` ADD FOREIGN KEY (`StaticQueueUID`) REFERENCES `mStaticQueues`(`StaticQueueUID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `tOrderFollowUp` CHANGE `QueueUID` `QueueUID` INT(11) NULL DEFAULT NULL;
ALTER TABLE `mReasons` ADD `StaticQueueUID` INT(11) NULL DEFAULT NULL AFTER `QueueUID`;
ALTER TABLE `mReasons` DROP FOREIGN KEY `mReasons_ibfk_1`; ALTER TABLE `mReasons` ADD CONSTRAINT `mReasons_ibfk_1` FOREIGN KEY (`StaticQueueUID`) REFERENCES `mStaticQueues`(`StaticQueueUID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `mStaticQueues` ADD `StaticQueueName` VARCHAR(200) NULL DEFAULT NULL AFTER `StaticQueueTableName`;

-- FHA and VA Lock Expiration Date column added in mCustomerWorkflowModules
ALTER TABLE `mCustomerWorkflowModules` ADD `FHALockExpirationDate` VARCHAR(100) NULL DEFAULT NULL AFTER `IsOrderRework`, ADD `VALockExpirationDate` VARCHAR(100) NULL DEFAULT NULL AFTER `FHALockExpirationDate`;

ALTER TABLE `tSubQueueCategory` CHANGE `CategoryUID` `CategoryUID` VARCHAR(50) NOT NULL;

-- Enable KickBack for workup
UPDATE `mCustomerWorkflowModules` SET `IsKickBackRequire` = 1 WHERE `WorkflowModuleUID` = 7 AND `CustomerUID` = 29;

-- KickBack alter query
ALTER TABLE `tOrderWorkflows` ADD `IsKickBack` TINYINT(1) NULL DEFAULT NULL AFTER `DueDateTime`, ADD `KickBackUserUID` INT(11) NULL DEFAULT NULL AFTER `IsKickBack`, ADD `KickBackDateTime` DATETIME NULL DEFAULT NULL AFTER `KickBackUserUID`;

-- mQueueColumns
ALTER TABLE `mQueueColumns` ADD `CreatedByUserUID` INT(11) NULL AFTER `Position`, ADD `CreatedDateTime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `CreatedByUserUID`, ADD `ModifiedByUserUID` INT(11) NULL AFTER `CreatedDateTime`, ADD `ModifiedDateTime` DATETIME NULL AFTER `ModifiedByUserUID`;
ALTER TABLE `mQueueColumns` ADD FOREIGN KEY (`CreatedByUserUID`) REFERENCES `mUsers`(`UserUID`) ON DELETE NO ACTION ON UPDATE NO ACTION; 
ALTER TABLE `mQueueColumns` ADD FOREIGN KEY (`ModifiedByUserUID`) REFERENCES `mUsers`(`UserUID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- Workup rework queue
ALTER TABLE `tOrderWorkflows` ADD `IsRework` TINYINT(1) NULL AFTER `ReversedRemarks`;

ALTER TABLE `mCustomer` ADD `NextPaymentDueRestriction` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Priority Report - Next payment due date Multiple Dates Provided Workup Enable Trigger to be restricted.' AFTER `ProductivityTarget`;

INSERT INTO `mStaticQueues` (`StaticQueueUID`, `StaticQueueTableName`, `StaticQueueName`, `ClientUID`, `Active`) VALUES (NULL, 'WorkupReworkOrdersTable', 'Rework', '29', '1');
INSERT INTO `mStaticQueues` (`StaticQueueUID`, `StaticQueueTableName`, `StaticQueueName`, `ClientUID`, `Active`) VALUES (NULL, 'ReWorkOrdersTable', 'Re-work', '29', '1');
INSERT INTO `mStaticQueues` (`StaticQueueUID`, `StaticQueueTableName`, `StaticQueueName`, `ClientUID`, `Active`) VALUES (NULL, 'ReWorkPendingOrdersTable', 'Re-work Pending', '29', '1');
INSERT INTO `mStaticQueues` (`StaticQueueUID`, `StaticQueueTableName`, `StaticQueueName`, `ClientUID`, `Active`) VALUES (NULL, 'FHAtblNewOrders', 'FHA New Orders', '29', '1');
INSERT INTO `mStaticQueues` (`StaticQueueUID`, `StaticQueueTableName`, `StaticQueueName`, `ClientUID`, `Active`) VALUES (NULL, 'ThreeAConfirmationOrdersTable', '3A Confirmation', '29', '1');

