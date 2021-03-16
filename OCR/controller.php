<?php 

function OCR($content){
	LOG_MSG('INFO','OCR(): START \n');
/* 
	 "docType"  input as PDF,JPEG,TIFF etc
	 "source"  Image source url link
	 "features" Select the fetures to perform selected feture detection
	 "featuresInput" Features Input url
	 "outputFormat" Output Response format
	 "outputLocation" Output location of the response
	 "engine" Selected search Engine. Google Vision, TOCR etc
	 "search" Search Keywords
*/
	 //Receive the RAW post data.

	 // $content = json_decode($data);
	 // $url= $content['source'];
	 // $parts = explode("/", $url);
	 // $content['source']= end($parts);

	$subDocDef = '{
		"Count": "23",
		"GEICO INSURANCE AGENCY, INC.": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.6",
			"subDocTypeUID": "7",
			"KeywordsCount": "6",
			"MandatoryKeywordsCount": "6",
			"MandatoryKeywords": [
				{"Header": ["GEICO","GEICO INSURANCE AGENCY","GEICO INSURANCE AGENCY, INC."]},
				{"Body": ["GEICO INSURANCE AGENCY"]},
				{"Footer": ["GEICO","GEICO INSURANCE AGENCY"]}
			],
			"Keywords": [
				{"Header": ["GEICO","GEICO INSURANCE AGENCY","GEICO INSURANCE AGENCY, INC."]},
				{"Body": ["GEICO INSURANCE AGENCY"]},
				{"Footer": ["GEICO","GEICO INSURANCE AGENCY"]}
			]
		},
		"ACORD": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.6",
			"subDocTypeUID": "8",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [
				{"Footer": ["ACORD","ACORD CORPORATION"]}
			],
			"Keywords": [
				{"Footer": ["ACORD","ACORD CORPORATION"]}
			]
		},
		"AAA": {
			"Header": "5",
			"Footer": "0",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.7",
			"subDocTypeUID": "2",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [
				{"Header": ["AAA"]}
			],
			"Keywords": [
				{"Header": ["AAA"]}
			]
		},
		"Champion Insurance Brokerage": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.7",
			"subDocTypeUID": "2",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [
				{"Header": ["Champion", "Champion Insurance Brokerage"]}
			],
			"Keywords": [
				{"Header": ["Champion", "Champion Insurance Brokerage"]}
			]
		},
		"Liberty Mutual": {
			"Header": "7",
			"Footer": "3",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.6",
			"subDocTypeUID": "7",
			"KeywordsCount": "3",
			"MandatoryKeywordsCount": "3",
			"MandatoryKeywords": [
				{"Header": ["Liberty Mutual", "Liber", "Mutual."]},
				{"Footer": ["Mutual."]}
			],
			"Keywords": [
				{"Header": ["Liberty Mutual", "Liber", "Mutual."]},
				{"Footer": ["Mutual."]}	
			]
		},
		"USAA": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "2",
			"LowerCaseSearch": "1",
			"subConfidence": "0.6",
			"subDocTypeUID": "8",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["USAA"]},{
				"Footer": ["USAA"]
			}],
			"Keywords": [{
				"Header": ["USAA"]},{
				"Footer": ["USAA"]
			}]
		},
		"Farmers Insurance": {
			"Header": "3",
			"Footer": "4",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.6",
			"subDocTypeUID": "7",
			"KeywordsCount": "3",
			"MandatoryKeywordsCount": "3",
			"MandatoryKeywords": [
				{"Header": ["Farmers Partner", "farmersinsurance"]},
				{"Footer": ["farmers"]}
			],
			"Keywords": [
				{"Header": ["Farmers Partner", "farmersinsurance"]},
				{"Footer": ["farmers"]}
			]
		},
	    "American Family Ins": {
	        "Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "14",
			"KeywordsCount": "3",
			"MandatoryKeywordsCount": "3",
			"MandatoryKeywords": [{
				"Header": ["American Family"]},{
				"Footer": ["American Standard","American Family"]
			}],
			"Keywords": [{
				"Header": ["American Family"]},{
				"Footer": ["American Standard","American Family"]
			}]
		},
		"Allstate": {
		    "Header": "15",
			"Footer": "7",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.6",
			"subDocTypeUID": "12",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [{
				"Header": ["ALLSTATE"]},{
				"Footer": ["eaxcis.allstate"]
			}],
			"Keywords": [{
				"Header": ["ALLSTATE"]},{
				"Footer": ["eaxcis.allstate"]
			}]
		},
		"UPC Insuracnce": {
			"Header": "3",
			"Footer": "3",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.7",
			"subDocTypeUID": "21",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [
				{"Header": ["From: Insurance 1st"]},
				{"Footer": ["UPC"]}
			],
			"Keywords": [
				{"Header": ["From: Insurance 1st"]},
				{"Footer": ["UPC"]}
			]
		},
	    "StateFarm": {
			"Header": "15",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.7",
			"subDocTypeUID": "21",
			"KeywordsCount": "4",
			"MandatoryKeywordsCount": "4",
			"MandatoryKeywords": [
				{"Header": ["STATE FARM", "statefarm"]},
				{"Body": ["STATE FARM", "statefarm"]},
				{"Footer": ["STATE FARM", "statefarm"]}
			],
			"Keywords": [
				{"Header": ["STATE FARM", "statefarm"]},
				{"Body": ["STATE FARM", "statefarm"]},
				{"Footer": ["STATE FARM", "statefarm"]}
			]
	    },
		"Amica Auto Home Life": {
			"Header": "3",
			"Footer": "3",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.7",
			"subDocTypeUID": "19",
			"KeywordsCount": "3",
			"MandatoryKeywordsCount": "3",
			"MandatoryKeywords": [{
				"Header": ["Amica","Amica Mutual Insurance Company"]},{
				"Footer": ["Amica"]
			}],
			"Keywords": [{
				"Header": ["Amica","Amica Mutual Insurance Company"]},{
				"Footer": ["Amica"]
			}]
	    },
		"MetLife Auto & Home": {
			"Header": "3",
			"Footer": "3",
			"KeyWordCutOff": "2",
			"LowerCaseSearch": "1",
			"subConfidence": "0.7",
			"subDocTypeUID": "18",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [
				{"Header": ["MetLife Auto & Home"]},
				{"Footer": ["MetLife Auto & Home"]}
			],
			"Keywords": [
				{"Header": ["MetLife Auto & Home"]},
				{"Footer": ["MetLife Auto & Home"]}
			]
		},
		"The Cincinnati Insurance Company": {
			"Header": "5",
			"Footer": "0",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.6",
			"subDocTypeUID": "5",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["Cincinnati"]
			}],
			"Keywords": [{
				"Header": ["Cincinnati"]
			}]
		},
		"Erie Insurance": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.7",
			"subDocTypeUID": "16",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [{
				"Header": ["Erie"]},{
				"Footer": ["Erie"]
			}],
			"Keywords": [{
				"Header": ["Erie"]},{
				"Footer": ["Erie"]
			}]
		},
		"Nationwide": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.7",
			"subDocTypeUID": "15",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["Nationwide"]},{
				"Footer": ["Nationwide"]
			}],
			"Keywords": [{
				"Header": ["Nationwide"]},{
				"Footer": ["Nationwide"]
			}]
		},
		"The Progressive Corporation": {
			"Header": "4",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.7",
			"subDocTypeUID": "17",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [
				{"Footer": ["Progressive Corporation", "affiliate of The Progressive Corporation"]}
			],
			"Keywords": [
				{"Footer": ["Progressive Corporation", "affiliate of The Progressive Corporation"]}
			]
		},
		"HARTFORD": {
			"Header": "3",
			"Footer": "3",
			"KeyWordCutOff": "3",
			"LowerCaseSearch": "0",
			"Confidence": "0.6",
			"docTypeUID": "9",
			"KeywordsCount": "4",
			"MandatoryKeywordsCount": "4",
			"MandatoryKeywords": [
				{"Header": ["HARTFORD"]},
				{"Body": ["HARTFORD"]},
				{"Footer": ["COUNTERSIGNED BY AUTHORIZED AGENT","facsimile"]}
			],
			"Keywords": [
				{"Header": ["HARTFORD"]},
				{"Body": ["HARTFORD"]},
				{"Footer": ["COUNTERSIGNED BY AUTHORIZED AGENT","facsimile"]}
			]
		},
		"Country Financial": {
			"Header": "5",
			"Footer": "8",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "0",
			"Confidence": "0.6",
			"docTypeUID": "9",
			"KeywordsCount": "3",
			"MandatoryKeywordsCount": "3",
			"MandatoryKeywords": [
				{"Header": ["COUNTRY\nFINANCIAL"]},
				{"Footer": ["COUNTRY Mutual","countryfinancial"]}
			],
			"Keywords": [
				{"Header": ["COUNTRY\nFINANCIAL"]},
				{"Footer": ["COUNTRY Mutual","countryfinancial"]}
			]
		},
		"Pemco Mutual": {
			"Header": "4",
			"Footer": "0",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "0",
			"Confidence": "0.6",
			"docTypeUID": "9",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [
				{"Header": ["PEMCO"]},
				{"Body": ["PEMCO Mutual Insurance"]}
			],
			"Keywords": [
				{"Header": ["PEMCO"]},
				{"Body": ["PEMCO Mutual Insurance"]}
			]
		},
		"STILLWATER INSURANCE": {
			"Header": "4",
			"Footer": "0",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "0",
			"Confidence": "0.6",
			"docTypeUID": "9",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [
				{"Header": ["STILLWATER\nINSURANCE GROUP"]}
			],
			"Keywords": [
				{"Header": ["STILLWATER\nINSURANCE GROUP"]}
			]
		},
		"Western Mutual Insurance": {
			"Header": "4",
			"Footer": "0",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.7",
			"subDocTypeUID": "15",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["Western Mutual Insurance"]}
			],
			"Keywords": [{
				"Header": ["Western Mutual Insurance"]}
			]
		},
		"Security First Insurance Company": {
			"Header": "4",
			"Footer": "4",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"subConfidence": "0.7",
			"subDocTypeUID": "15",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["Security First Insurance Company"]},
				{"Footer": ["Security First Insurance Company"]}
			],
			"Keywords": [{
				"Header": ["Security First Insurance Company"]},
				{"Footer": ["Security First Insurance Company"]}
				]
		}
	}';	 

	$docDef = '{ 
		"Count": "34",
		"HOMESITE INSURANCE COMPANY": {
			"Header": "5",
			"Footer": "0",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "1",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["Issued by HOMESITE INSURANCE COMPANY"]
			}],
			"Keywords": [{
				"Header": ["Issued by HOMESITE INSURANCE COMPANY"]
			}]
		},
		"CSAA GENERAL INSURANCE COMPANY": {
			"Header": "10",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "0",
			"Confidence": "0.7",
			"docTypeUID": "2",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["CSAA General Insurance"]
			}],
			"Keywords": [{
				"Header": ["CSAA General Insurance"]
			}]
		},
		"CSAA INSURANCE EXCHANGE": {
			"Header": "15",
			"Footer": "15",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "0",
			"Confidence": "0.7",
			"docTypeUID": "3",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["CSAA Insurance Exchange"]
			}],
			"Keywords": [{
				"Header": ["CSAA Insurance Exchange"]
			}]
		},
		"ADIRONDACK INSURANCE EXCHANGE": {
			"Header": "5",
			"Footer": "0",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "4",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["DIRONDACK"]
			}],
			"Keywords": [{
				"Header": ["DIRONDACK"]
			}]
		},
		"The Cincinnati Insurance Company": {
			"Header": "5",
			"Footer": "0",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "5",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["Cincinnati"]
			}],
			"Keywords": [{
				"Header": ["Cincinnati"]
			}]
		},
		"Travelers Commercial Insurance Company": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "7",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [
				{"Header": ["Travelers Commercial","Travelers Commercial Insurance"]},
				{"Body": ["Travelers Insurance Company", "Travelers Commercial Insurance"]}
			],
			"Keywords": [
				{"Header": ["Travelers Commercial","Travelers Commercial Insurance"]},
				{"Body": ["Travelers Insurance Company", "Travelers Commercial Insurance"]}
			]
		},
		"THE TRAVELERS HOME AND MARINE INSURANCE COMPANY": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "7",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["THE TRAVELERS HOME"]},{
				"Body": ["THE TRAVELERS HOME AND MARINE INSURANCE COMPANY"]},{
				"Footer": ["TRAVELERS PERSONAL INSURANCE"]
			}],
			"Keywords": [{
				"Header": ["THE TRAVELERS HOME"]},{
				"Body": ["THE TRAVELERS HOME AND MARINE INSURANCE COMPANY"]},{
				"Footer": ["TRAVELERS PERSONAL INSURANCE"]
			}]
		},
		"THE TRAVELERS INDEMNITY COMPANY": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "7",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["THE TRAVELERS INDEMNITY"]},{
				"Body": ["THE TRAVELERS INDEMNITY COMPANY"]},{
				"Footer": ["TRAVELERS PERSONAL INSURANCE"]
			}],
			"Keywords": [{
				"Header": ["THE TRAVELERS INDEMNITY"]},{
				"Body": ["THE TRAVELERS INDEMNITY COMPANY"]},{
				"Footer": ["TRAVELERS PERSONAL INSURANCE"]
			}]
		},
		"TRAVELERS SPP": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "7",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [
				{"Header": ["SPP"]},
				{"Body": ["SPP"]}
			],
			"Keywords": [
				{"Header": ["SPP"]},
				{"Body": ["SPP"]}
			]
		},
		"UNITED SERVICES AUTOMOBILE ASSOCIATION": {
			"Header": "3",
			"Footer": "4",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "21",
			"KeywordsCount": "4",
			"MandatoryKeywordsCount": "4",
			"MandatoryKeywords": [
				{"Header": ["USAA", "USAA Fax Server"]},
				{"Footer": ["USAA", "USAA Fax Server"]}
			],
			"Keywords": [
				{"Header": ["USAA", "USAA Fax Server"]},
				{"Footer": ["USAA", "USAA Fax Server"]}
			]
		},
		"Hartford": {
			"Header": "3",
			"Footer": "3",
			"KeyWordCutOff": "3",
			"LowerCaseSearch": "0",
			"Confidence": "0.6",
			"docTypeUID": "9",
			"KeywordsCount": "4",
			"MandatoryKeywordsCount": "4",
			"MandatoryKeywords": [{
				"Header": ["Hartford"]},{
				"Footer": ["COUNTERSIGNED BY", "COUNTERSIGNED BY AUTHORIZED AGENT","facsimile"]
			}],
			"Keywords": [{
				"Header": ["Hartford"]},{
				"Footer": ["COUNTERSIGNED BY", "COUNTERSIGNED BY AUTHORIZED AGENT","facsimile"]
			}]
		},
		"LIBERTY MUTUAL": {
			"Header": "8",
			"Footer": "10",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "10",
			"KeywordsCount": "9",
			"MandatoryKeywordsCount": "9",
			"MandatoryKeywords": [{
				"Header": ["Liberty Mutual","Mutual.\nINSURANCE","Liberty Insurance Corporation", "Liberty\nMutual\nINSURANCE"]},{
				"Body": ["Liberty Mutual","Mutual.\nINSURANCE"]},{
				"Footer": ["Liberty Mutual","Mutual.\nINSURANCE","Liberty Insurance Corporation"]
			}],
			"Keywords": [{
				"Header": ["Liberty Mutual","Mutual.\nINSURANCE","Liberty Insurance Corporation", "Liberty\nMutual\nINSURANCE"]},{
				"Body": ["Liberty Mutual","Mutual.\nINSURANCE"]},{
				"Footer": ["Liberty Mutual","Mutual.\nINSURANCE","Liberty Insurance Corporation"]
			}]
		},
		"state Farm Lloyds": {
			"Header": "15",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "11",
			"KeywordsCount": "3",
			"MandatoryKeywordsCount": "3",
			"MandatoryKeywords": [
				{"Header": ["State Farm Lloyds"]},
				{"Body": ["State Farm Lloyds"]},
				{"Footer": ["State Farm Lloyds"]}
			],
			"Keywords": [
				{"Header": ["State Farm Lloyds"]},
				{"Body": ["State Farm Lloyds"]},
				{"Footer": ["State Farm Lloyds"]}
			]
		},
		"State Farm Fire and Casualty Company": {
			"Header": "20",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "11",
			"KeywordsCount": "4",
			"MandatoryKeywordsCount": "4",
			"MandatoryKeywords": [
				{"Header": ["State Farm Fire and Casualty Company"]},
				{"Body": ["State Farm Fire and Casualty Company"]}
			],
			"Keywords": [
				{"Header": ["State Farm Fire and Casualty Company"]},
				{"Body": ["State Farm Fire and Casualty Company"]}
			]
		},
		"ALLSTATE PROPERTY AND CASUALTY INSURANCE": {
			"Header": "15",
			"Footer": "7",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "12",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["ALLSTATE PROPERTY & CASUALTY"]}],
			"Keywords": [{
				"Header": ["ALLSTATE PROPERTY & CASUALTY"]}]
		},
		"ALLSTATE VEHICLE AND PROPERTY INS": {
			"Header": "15",
			"Footer": "7",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "12",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["ALLSTATE VEHICLE AND PROPERTY"]}],
			"Keywords": [{
				"Header": ["ALLSTATE VEHICLE AND PROPERTY"]}]
		},
		"AllState Insurance Comapny": {
			"Header": "15",
			"Footer": "3",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "12",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [{
				"Header": ["ALLSTATE INSURANCE COMPANY"]},{
				"Footer": ["eaxcis.allstate"]
			}],
			"Keywords": [{
				"Header": ["ALLSTATE INSURANCE COMPANY"]},{
				"Footer": ["eaxcis.allstate"]
			}]
		},
		"FARMERS": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.6",
			"docTypeUID": "13",
			"KeywordsCount": "4",
			"MandatoryKeywordsCount": "4",
			"MandatoryKeywords": [{
				"Header": ["Farmers Partner Inquiry"]},{
				"Body": ["FARMERS INSURANCE EXCHANGE"]},{
				"Footer": ["farmersinsurance", "farmers"]
			}],
			"Keywords": [{
				"Header": ["Farmers Partner Inquiry"]},{
				"Body": ["FARMERS INSURANCE EXCHANGE"]},{
				"Footer": ["farmersinsurance", "farmers"]
			}]
		},
		"American Family Insurance Company": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "14",
			"KeywordsCount": "4",
			"MandatoryKeywordsCount": "4",
			"MandatoryKeywords": [{
				"Header": ["American Family", "American Family Insurance"]},{
				"Footer": ["American Standard","American Family"]
			}],
			"Keywords": [{
				"Header": ["American Family", "American Family Insurance"]},{
				"Footer": ["American Standard","American Family"]
			}]
		},
		"AMERICAN STRATEGIC INSURANCE CORP": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "14",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [
				{"Header": ["ASI", "AMERICAN STRATEGIC INSURANCE CORP"]},
				{"Footer": ["ASI","ASI Group"]}
			],
			"Keywords": [
				{"Header": ["ASI", "AMERICAN STRATEGIC INSURANCE CORP"]},
				{"Footer": ["ASI","ASI Group"]}
			]
		},
		"Nationwide": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "15",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["Nationwide"]},{
				"Footer": ["Nationwide"]
			}],
			"Keywords": [{
				"Header": ["Nationwide"]},{
				"Footer": ["Nationwide"]
			}]
		},
		"Erie": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "16",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [{
				"Header": ["Erie"]},{
				"Footer": ["Erie"]
			}],
			"Keywords": [{
				"Header": ["Erie"]},{
				"Footer": ["Erie"]
			}]
		},
		"Progressive": {
			"Header": "5",
			"Footer": "5",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "17",
			"KeywordsCount": "3",
			"MandatoryKeywordsCount": "3",
			"MandatoryKeywords": [{
				"Header": ["AMERICAN STRATEGIC INSURANCE CORP"]},{
				"Footer": ["Progressive Corporation","ASI Group"]
			}],
			"Keywords": [{
				"Header": ["AMERICAN STRATEGIC INSURANCE CORP"]},{
				"Footer": ["Progressive Corporation","ASI Group"]
			}]
		},
		"Metropolitan Property and Casualty Insurance Company": {
			"Header": "3",
			"Footer": "3",
			"KeyWordCutOff": "2",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "18",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [{
				"Header": ["Metropolitan Property","Metropolitan Property and Casualty Insurance Company"]},{
				"Footer": ["Metropolitan Property","Metropolitan Property and Casualty Insurance Company"]
			}],
			"Keywords": [{
				"Header": ["Metropolitan Property","Metropolitan Property and Casualty Insurance Company"]},{
				"Footer": ["Metropolitan Property","Metropolitan Property and Casualty Insurance Company"]
			}]
		},
		"Amica Mutual Insurance Company": {
			"Header": "3",
			"Footer": "3",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "19",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [{
				"Header": ["Amica"]},{
				"Footer": ["Amica"]
			}],
			"Keywords": [{
				"Header": ["Amica"]},{
				"Footer": ["Amica"]
			}]
		},
		"Auto Club Family Insurance Company": {
			"Header": "2",
			"Footer": "8",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "20",
			"KeywordsCount": "3",
			"MandatoryKeywordsCount": "3",
			"MandatoryKeywords": [{
				"Header": ["ACSC","Auto Club Family Insurance Company"]},{
				"Footer": ["Auto Club"]
			}],
			"Keywords": [{
				"Header": ["ACSC","Auto Club Family Insurance Company"]},{
				"Footer": ["Auto Club"]
			}]
		},
		"FAMILY SECURITY INSURANCE COMPANY": {
			"Header": "3",
			"Footer": "3",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "21",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [{
				"Header": ["FAMILY SECURITY INSURANCE COMPANY"]},{
				"Footer": ["FSIC"]
			}],
			"Keywords": [{
				"Header": ["FAMILY SECURITY INSURANCE COMPANY"]},{
				"Footer": ["FSIC"]
			}]
		},
		"UNITED PROPERTY & CASUALTY INSURANCE COMPANY": {
			"Header": "3",
			"Footer": "4",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "1",
			"Confidence": "0.7",
			"docTypeUID": "21",
			"KeywordsCount": "3",
			"MandatoryKeywordsCount": "3",
			"MandatoryKeywords": [{
				"Header": ["UNITED PROPERTY", "UNITED PROPERTY & CASUALTY INS", "UNITED PROPERTY & CASUALTY INSURANCE COMPANY"]},{
				"Footer": ["UPC"]
			}],
			"Keywords": [{
				"Header": ["UNITED PROPERTY", "UNITED PROPERTY & CASUALTY INS", "UNITED PROPERTY & CASUALTY INSURANCE COMPANY"]},{
				"Footer": ["UPC"]
			}]
		},
		"Hippo": {
			"Header": "3",
			"Footer": "3",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "0",
			"Confidence": "0.6",
			"docTypeUID": "9",
			"KeywordsCount": "3",
			"MandatoryKeywordsCount": "3",
			"MandatoryKeywords": [
				{"Header": ["hippo"]},
				{"Body": ["hippo"]},
				{"Footer": ["hippo"]}
			],
			"Keywords": [
				{"Header": ["hippo"]},
				{"Body": ["hippo"]},
				{"Footer": ["hippo"]}
			]
		},
		"Country Financial": {
			"Header": "5",
			"Footer": "8",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "0",
			"Confidence": "0.6",
			"docTypeUID": "9",
			"KeywordsCount": "3",
			"MandatoryKeywordsCount": "3",
			"MandatoryKeywords": [
				{"Header": ["COUNTRY\nFINANCIAL"]},
				{"Footer": ["COUNTRY Mutual","countryfinancial"]}
			],
			"Keywords": [
				{"Header": ["COUNTRY\nFINANCIAL"]},
				{"Footer": ["COUNTRY Mutual","countryfinancial"]}
			]
		},
		"Pemco Mutual": {
			"Header": "4",
			"Footer": "0",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "0",
			"Confidence": "0.6",
			"docTypeUID": "9",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [
				{"Header": ["PEMCO"]},
				{"Body": ["PEMCO Mutual Insurance"]}
			],
			"Keywords": [
				{"Header": ["PEMCO"]},
				{"Body": ["PEMCO Mutual Insurance"]}
			]
		},
		"STILLWATER": {
			"Header": "4",
			"Footer": "0",
			"KeyWordCutOff": "2",
			"LowerCaseSearch": "0",
			"Confidence": "0.6",
			"docTypeUID": "9",
			"KeywordsCount": "2",
			"MandatoryKeywordsCount": "2",
			"MandatoryKeywords": [
				{"Header": ["STILLWATER"]},
				{"Body": ["Stillwater"]}
			],
			"Keywords": [
				{"Header": ["STILLWATER"]},
				{"Body": ["Stillwater"]}
			]
		},
		"Western Mutual Insurance Company": {
			"Header": "4",
			"Footer": "0",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "0",
			"Confidence": "0.6",
			"docTypeUID": "9",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [
				{"Header": ["Western Mutual Insurance Company"]}
			],
			"Keywords": [
				{"Header": ["Western Mutual Insurance Company"]}
			]
		},
		"Security First Insurance Company": {
			"Header": "4",
			"Footer": "4",
			"KeyWordCutOff": "1",
			"LowerCaseSearch": "0",
			"Confidence": "0.6",
			"docTypeUID": "9",
			"KeywordsCount": "1",
			"MandatoryKeywordsCount": "1",
			"MandatoryKeywords": [
				{"Header": ["Security First Insurance Company"]},
				{"Footer": ["Security First Insurance Company"]}
			],
			"Keywords": [
				{"Header": ["Security First Insurance Company"]},
				{"Footer": ["Security First Insurance Company"]}
			
			]
		}
	}';

if(IS_LIVE){
$allData = array(
	"sourceApp"=> $content['sourceApp'],
		"orderNo"=> $content['orderNo'],
		"orderUID"=> $content['orderUID'], 
		"docType" => $content['docType'],
		"source" => $content['source'],
		"features" => $content['features'],
		"featuresInput" => $content['featuresInput'],
		"outputFormat" => $content['outputFormat'],
		"outputLocation" => $content['outputLocation'],
		"IsFaxImage" => $content['IsFaxImage'],
		"BorrowerNames" => $content['BorrowerNames'],
		"engine" => $content['engine'],
		"docDef" => $docDef,
		"subDocDef" 		=> $subDocDef,
	);
}else{
$allData = array(
		"sourceApp"=> "StacX",
		"orderNo"=> "libertymutual2_geico_t1",
		"orderUID"=> "25458", 
		"docType" => "pdf",
		"source" => "/var/www/Insig/scripts/New/docs/DoctracPDF/LIBERTY-MUTUAL/GEICO/Template1/libertymutual2_geico_t1.pdf",
		"IsFaxImage" => FALSE,
		"BorrowerNames" => [],
		"features" => "DOCUMENT_TEXT_DETECTION",
		//"featuresInput" => $_POST['featuresInput'],
		//"outputFormat" => $_POST['outputFormat'],
		//"outputLocation" => $_POST['outputLocation'],
		"engine" => "Google Vision",
		"docDef" => $docDef,
		"subDocDef" 	=> $subDocDef
	);
}
	if(DEBUG) print_arr($allData);
	
	$response = '';
	//Switch case for selecting the Search Engine
	switch($allData['engine']){
		case 'Google Vision':
			$response = googlevision($allData);
		break;
		case 'TESSERACT':

		//check is fax image and if valid check for order number and borrower name if order number is not found
		if(isset($allData['IsFaxImage']) && $allData['IsFaxImage']) {
			$response = efaxtessaract($allData);
		} else {
			$response = tessaract($allData);
		}
		break;
		case 'TEXTRACT':
			if(DEBUG) echo "Invalid AWS search engine!";
		break;
		default :
		  if(DEBUG) echo "No OCR Engine Matched";
	}
	LOG_MSG('INFO','OCR(): END \n');
	return $response;
} 
