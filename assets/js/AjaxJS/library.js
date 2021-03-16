

var SendAsyncAjaxRequest = async (RequestType, ReqeustURL, RequestData, ResponseDataType, processData, contentType, BeforeSend_CallBack) => {
    if (ResponseDataType == '') {
        ResponseDataType = 'text/html';
    }
	var ajaxoptions = {
		type: RequestType,
		url: base_url + ReqeustURL,
		data: RequestData,
		dataType: ResponseDataType,
		cache: false,
		beforeSend: BeforeSend_CallBack,
	}

	if (processData == false) {
		ajaxoptions.processData = processData;
	}

	if (contentType == false) {
		ajaxoptions.contentType = contentType;
	}

	if (typeof this.processData === 'undefined') {
		this.processData = true;
	}
	if (typeof this.contentType === 'undefined') {
		this.contentType = true;
	}
	console.log(ajaxoptions);
	return new Promise(function (resolve, reject) {
		resolve(
			$.ajax(ajaxoptions)
		);

	})
}
