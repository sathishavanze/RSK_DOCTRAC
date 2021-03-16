var InvestorName = "";
var LenderUID = "";
var ProjectUID = "";
var filetoupload = [];
var callUpdateTime = 45000;
function _asyncToGenerator(fn) {
	return function () {
		var gen = fn.apply(this, arguments);
		return new Promise(function (resolve, reject) {
			function step(key, arg) {
				try {
					var info = gen[key](arg);
					var value = info.value;
				} catch (error) {
					reject(error);
					return;
				}
				if (info.done) {
					resolve(value);
				} else {
					return Promise.resolve(value).then(
						function (value) {
							step("next", value);
						},
						function (err) {
							step("throw", err);
						}
					);
				}
			}
			return step("next");
		});
	};
}

var OrderEntryBtnID = "";
var btnclearexception_value = "";
var $button = $(this);
$(function () {
	$(document)
		.off("click", ".cancel")
		.on("click", ".cancel", function (e) {
			var OrderUID = $("#OrderUID").val();
			swal({
				title: "Are you sure?",
				text: "Do you want to discard the order!",
				type: "warning",
				showCancelButton: true,
				confirmButtonText: "Yes, do it!",
				cancelButtonText: "No, keep it",
				confirmButtonClass: "btn btn-success",
				cancelButtonClass: "btn btn-danger",
				buttonsStyling: false,
			}).then(
				function (confirm) {
					$.ajax({
						type: "POST",
						url: base_url + "CommonController/UpdateNullStatus",
						data: {
							OrderUID: OrderUID,
						},
						dataType: "json",
						beforeSend: function () {
							addcardspinner("#Orderentrycard");
						},

						success: function (response) {
							if (response.validation_error == 0) {
								$.notify(
									{
										icon: "icon-bell-check",
										message: response.message,
									},
									{
										type: "success",
										delay: 1000,
									}
								);

								if (check_is_url_contains_string_value(window.location.href)) {
									window.location.href = base_url + response["URL"];
								} else {
									triggerpage(base_url + response["URL"]);
								}
							} else {
								$.notify(
									{
										icon: "icon-bell-check",
										message: response.message,
									},
									{
										type: "danger",
										delay: 1000,
									}
								);
							}
						},
					});
				},
				function (dismiss) {}
			);
		});

	$(document)
		.off("change", "#Customer")
		.on("change", "#Customer", function (e) {
			var CustomerUID = $(this).val();
			var id = $(this).attr("id");

			AjaxGetCustomerProjects(CustomerUID)
				.then(function (response) {
					var Products = response.Products;

					Product_select = Products.reduce((accumulator, value) => {
						return (
							accumulator +
							'<Option value="' +
							value.ProductUID +
							'">' +
							value.ProductName +
							"</Option>"
						);
					}, "");

					if (id == "Customer") {
						if ($("#Single-ProductUID").hasClass("is-invalid")) {
							$("#Single-ProductUID")
								.removeClass("is-invalid")
								.closest(".form-group")
								.removeClass("has-danger");
						}
						$("#Single-ProductUID").html(Product_select);
						$("#Single-ProductUID")
							.val($("#Single-ProductUID").find("option:first").val())
							.trigger("change");
						GetLoanTypeDetails(CustomerUID);
					} else if (id == "bulk_Customers") {
						if ($("#bulk_ProjectUID").hasClass("is-invalid")) {
							$("#bulk_ProjectUID")
								.removeClass("is-invalid")
								.closest(".form-group")
								.removeClass("has-danger");
						}
						$("#bulk_ProjectUID").html(Project_select);
						$("#bulk_ProjectUID")
							.val($("#bulk_ProjectUID").find("option:first").val())
							.trigger("change");
					}

					removecardspinner("#Orderentrycard");
					callselect2();
				})
				.catch((jqXHR) => {
					console.log(jqXHR);
				});
		});

	function GetLoanTypeDetails(CustomerUID) {
		$.ajax({
			type: "POST",
			url: base_url + "Orderentry/GetLoanTypeDetails",
			data: { CustomerUID: CustomerUID },
			success: function (data) {
				data = JSON.parse(data);
				$("#LoanType").empty();
				$("#LoanType").append("<option value=''></option>");
				$.each(data, function (k, v) {
					$("#LoanType").append(
						"<option value='" +
							v.LoanTypeName +
							"'>" +
							v.LoanTypeName +
							"</option>"
					);
				});
				$("#LoanType").trigger("change.select2");
			},
			error: function (jqXHR) {},
		});
	}

	$(document)
		.off("change", "#bulk_Customers")
		.on("change", "#bulk_Customers", function (e) {
			var CustomerUID = $(this).val();
			var id = $(this).attr("id");
			$("#bulk_ProductUID").empty();
			$("#bulk_ProjectUID").empty();
			$("#bulk_LenderUID").empty();
			$(".changeentryfilename").attr("disabled", true).addClass("disabled");
			$(".changeentryxmlfilename").attr("disabled", true).addClass("disabled");
			AjaxGetCustomerProducts(CustomerUID)
				.then(function (response) {
					var CustomerProducts = response.CustomerProducts;

					Product_select = CustomerProducts.reduce((accumulator, value) => {
						return (
							accumulator +
							'<Option value="' +
							value.ProductUID +
							'" data-type="' +
							value.BulkImportFormat +
							'" data-typename="' +
							value.BulkImportTemplateName +
							'"  data-typexmlname="' +
							value.BulkImportTemplateXMLName +
							'" data-bulkassignformat="' +
							value.BulkAssignFormat +
							'" data-bulkassigntemplatename="' +
							value.BulkAssignTemplateName +
							'" data-payoffbulkupdateformat="' +
							value.PayOffBulkUpdateFormat +
							'" data-payoffbulkupdatetemplatename="' +
							value.PayOffBulkUpdateTemplateName +
							'" data-bulkworkflowenableformat="' +
							value.BulkWorkflowEnableFormat +
							'" data-bulkworkflowenabletemplatename="' +
							value.BulkWorkflowEnableTemplateName +
							'" data-docsoutbulkupdateformat="' +
							value.DocsOutBulkUpdateFormat +
							'" data-docsoutbulkupdatetemplatename="' +
							value.DocsOutBulkUpdateTemplateName +
							'">' +
							value.ProductName +
							"</Option>"
						);
					}, "");

					$("#bulk_products").html(Product_select);

					if (CustomerProducts.length > 0) {
						$("#bulk_products")
							.val($("#bulk_products").find("option:first").val())
							.trigger("change");
					}

					removecardspinner("#Orderentrycard");
					callselect2();
				})
				.catch((jqXHR) => {
					console.log(jqXHR);
				});
		});

	$(document)
		.off("change", "#Single-ProductUID")
		.on("change", "#Single-ProductUID", function (e) {
			var currentrow = $("#Single-ProductUID").closest(
				".productsubproduct_row"
			);
			var ProductUID = $("#Single-ProductUID").val();
			var CustomerUID = $("#Customer").val();

			var ProjectUID = $("#Single-ProjectUID")
				.children("option:selected")
				.val();

			var dataobject = {
				ProductUID: ProductUID,
				CustomerUID: CustomerUID,
			};

			Fetch_Product_DocType(dataobject);
		});

	/*ZipCode Change function*/

	$(document)
		.off("blur", "#PropertyZipcode")
		.on("blur", "#PropertyZipcode", function (event) {
			zip_val = $(this).val();
			if (zip_val != "") {
				addcardspinner("#Orderentrycard");
				$.ajax({
					type: "POST",
					url: base_url + "CommonController/GetZipCodeDetails",
					data: {
						Zipcode: zip_val,
					},
					dataType: "json",
					cache: false,
					beforeSend: function () {
						addcardspinner("#Orderentrycard");
					},
					success: function (data) {
						$(".PropertyCityName").empty();
						$(".PropertyStateCode").empty();
						$(".PropertyCountyName").empty();
						$(".MultiOrderedcity").html(" ");
						$(".MultiOrderedcounty").html(" ");
						$(".MultiOrderedstate").html(" ");

						if (data != "") {
							if (data["success"] == 1) {
								$("#zipcodeadd").hide();

								if (data["City"].length > 1) {
									$(".MultiOrderedcity").html(" ");
									$(".MultiOrderedcity").append(
										'<span class="badge badge-danger cus-badge" style="background: #eb6357;color: #fff; z-index: 9999; top: -16px; right: -20px;">' +
											data["City"].length +
											"</span>"
									);
								}

								if (data["County"].length > 1) {
									$(".MultiOrderedcounty").html(" ");
									$(".MultiOrderedcounty").append(
										'<span class="badge badge-danger cus-badge" style="background: #eb6357;color: #fff; z-index: 9999; top: -16px; right: -20px;">' +
											data["County"].length +
											"</span>"
									);
								}

								if (data["State"].length > 1) {
									$(".MultiOrderedstate").html(" ");
									$(".MultiOrderedstate").append(
										'<span class="badge badge-danger cus-badge" style="background: #eb6357;color: #fff; z-index: 9999; top: -16px; right: -20px;">' +
											data["State"].length +
											"</span>"
									);
								}

								$.each(data["City"], function (k, v) {
									$("#PropertyCityName").val(v["CityName"]);
									$(".PropertyCityName").append(
										'<li><a href="javascript:(void);" data-value="' +
											v["CityName"] +
											'">' +
											v["CityName"] +
											"</a></li>"
									);
									$("#PropertyCityName")
										.closest(".form-group")
										.addClass("is-filled");
									zipcode_select();
								});

								$.each(data["County"], function (k, v) {
									$("#PropertyCountyName").val(v["CountyName"]);
									$(".PropertyCountyName").append(
										'<li><a href="javascript:(void);" data-value="' +
											v["CountyName"] +
											'">' +
											v["CountyName"] +
											"</a></li>"
									);
									$("#PropertyCountyName")
										.closest(".form-group")
										.addClass("is-filled");
									zipcode_select();
								});

								$.each(data["State"], function (k, v) {
									$("#PropertyStateCode").val(v["StateCode"]);
									$(".PropertyStateCode").append(
										'<li><a href="javascript:(void);" data-value="' +
											v["StateCode"] +
											'">' +
											v["StateCode"] +
											"</a></li>"
									);
									$("#PropertyStateCode")
										.closest(".form-group")
										.addClass("is-filled");
									zipcode_select();
								});

								$("#PropertyStateCode,#PropertyCountyName,#PropertyCityName")
									.removeClass("is-invalid")
									.closest(".form-group")
									.removeClass("has-danger");
								$(
									"#PropertyStateCode.select2picker,#PropertyCountyName.select2picker,#PropertyCityName.select2picker"
								)
									.next()
									.find("span.select2-selection")
									.removeClass("errordisplay");
							} else {
								$("#PropertyCityName").val("");
								$("#PropertyCityName")
									.closest(".form-group")
									.removeClass("is-filled");

								$("#PropertyCountyName").val("");
								$("#PropertyCountyName")
									.closest(".form-group")
									.removeClass("is-filled");

								$("#PropertyStateCode").val("");
								$("#PropertyStateCode")
									.closest(".form-group")
									.removeClass("is-filled");

								$("#zipcodeadd").show();
							}
						}
						removecardspinner("#Orderentrycard");
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
					failure: function (jqXHR, textStatus, errorThrown) {
						console.log(errorThrown);
					},
				});
			} else {
				$("#PropertyCityName").val("");
				$("#PropertyCityName").closest(".form-group").removeClass("is-filled");

				$("#PropertyCountyName").val("");
				$("#PropertyCountyName")
					.closest(".form-group")
					.removeClass("is-filled");

				$("#PropertyStateCode").val("");
				$("#PropertyStateCode").closest(".form-group").removeClass("is-filled");
			}
		});

	$(document)
		.off("click", "#discard")
		.on("click", "#discard", function (e) {
			/*SWEET ALERT CONFIRMATION*/
			swal({
				title:
					'<div class="text-primary" id="iconchg"><i style="font-size: 40px;" class="fa fa-info-circle fa-5x"></i></div>',
				html:
					'<span id="modal_msg" class= "modal_spanheading" > Are you sure want to cancel this Order ?</span>',
				showCancelButton: true,
				confirmButtonClass: "btn btn-success",
				cancelButtonClass: "btn btn-danger",
				buttonsStyling: false,
				closeOnClickOutside: false,
				allowOutsideClick: false,
				showLoaderOnConfirm: true,
				position: "top-end",
			}).then(
				function (confirm) {
					$('a[data-dismiss="alert"]').trigger("click");
					var OrderUID = $("#OrderUID").val();

					$.ajax({
						type: "POST",
						url: base_url + "OrderComplete/OrderCancellation",
						data: {
							OrderUID: OrderUID,
						},
						dataType: "json",
						cache: false,
						beforeSend: function () {
							addcardspinner("#Orderentrycard");
						},
						success: function (data) {
							if (data.validation_error == 0) {
								/*Sweet Alert MSG*/
								$.notify(
									{
										icon: "icon-bell-check",
										message: data["message"],
									},
									{
										type: "success",
										delay: 1000,
									}
								);
								disposepopover();
								if (check_is_url_contains_string_value(window.location.href)) {
									window.location.href = base_url + "MyOrders";
								} else {
									triggerpage(base_url + "MyOrders");
								}
							} else {
								swal({
									title: "<i class='icon-close2 icondanger'></i>",
									html: "<p>" + data.message + "</p>",
									confirmButtonClass: "btn btn-success",
									allowOutsideClick: false,
									width: "300px",
									buttonsStyling: false,
								}).catch(swal.noop);
							}
						},
						error: function (jqXHR) {
							swal({
								title: "<i class='icon-close2 icondanger'></i>",
								html: "<p>Failed to Complete</p>",
								confirmButtonClass: "btn btn-success",
								allowOutsideClick: false,
								width: "300px",
								buttonsStyling: false,
							}).catch(swal.noop);
						},
					});
				},
				function (dismiss) {}
			);
		});

	$(document)
		.off("click", "#reviewcomplete")
		.on("click", "#reviewcomplete", function (e) {
			var button = $(this);
			var button_text = $(this).html();

			/*SWEET ALERT CONFIRMATION*/
			swal({
				title:
					'<div class="text-primary" id="iconchg"><i style="font-size: 40px;" class="fa fa-info-circle fa-5x"></i></div>',
				html:
					'<span id="modal_msg" class= "modal_spanheading" > Do you want to complete Review ?</span>',
				showCancelButton: true,
				confirmButtonClass: "btn btn-success",
				cancelButtonClass: "btn btn-danger",
				buttonsStyling: false,
				closeOnClickOutside: false,
				allowOutsideClick: false,
				showLoaderOnConfirm: true,
				position: "bottom-middle",
			}).then(
				function (confirm) {
					$(button).prop("disabled", true);
					$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing');

					if (is_url_contains_string("Indexing")) {
						fn_save_stacking()
							.then(function (response) {
								fn_review_complete(button, button_text);
							})
							.catch(function (error) {});
					} else if (is_url_contains_string("Audit")) {
						fn_autosave_auditing()
							.then(function (response) {
								fn_review_complete(button, button_text);
							})
							.catch(function (error) {});
					} else {
						fn_review_complete(button, button_text);
					}
				},
				function (dismiss) {}
			);
		});

	$(document)
		.off("submit", "#raiseexcetion")
		.on("submit", "#raiseexcetion", function (e) {
			e.preventDefault();
			e.stopPropagation();
			var OrderUID = $("#OrderUID").val();

			var button = $(".btnraiseexcetion");
			var button_text = $(".btnraiseexcetion").html();

			$(button).prop("disabled", true);
			$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Raising');

			var formdata = new FormData($(this)[0]);
			formdata.append("OrderUID", OrderUID);

			if (is_url_contains_string("Indexing")) {
				fn_save_stacking()
					.then(function (response) {
						fn_raise_exception(formdata, button, button_text);
					})
					.catch(function (error) {});
			} else if (is_url_contains_string("Audit")) {
				fn_autosave_auditing()
					.then(function (response) {
						fn_raise_exception(formdata, button, button_text);
					})
					.catch(function (error) {});
			} else {
				fn_raise_exception(formdata, button, button_text);
			}
		});

	$(document)
		.off("click", ".btnclearexception")
		.on("click", ".btnclearexception", function (e) {
			btnclearexception_value = $(this).val();
			$button = $(this);
		});

	$(document)
		.off("submit", "#frmclearexception")
		.on("submit", "#frmclearexception", function (e) {
			e.preventDefault();
			e.stopPropagation();
			var OrderUID = Const_ORDERUID;

			// var $button = $('.btnclearexception');
			var button = $button;
			var button_text = $button.html();

			$(button).prop("disabled", true);
			$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Clearing');

			// var $submitbuttons = $('.btnclearexception');

			var formdata = new FormData($(this)[0]);
			formdata.append("OrderUID", OrderUID);
			formdata.append("submit", btnclearexception_value);

			if (is_url_contains_string("Indexing")) {
				fn_save_stacking()
					.then(function (response) {
						fn_clear_exception(formdata, button, button_text);
					})
					.catch(function (error) {});
			} else if (is_url_contains_string("Audit")) {
				fn_autosave_auditing()
					.then(function (response) {
						fn_clear_exception(formdata, button, button_text);
					})
					.catch(function (error) {});
			} else {
				fn_clear_exception(formdata, button, button_text);
			}
		});

	$(document)
		.off("submit", "#frm_orderreverse")
		.on("submit", "#frm_orderreverse", function (e) {
			e.preventDefault();
			e.stopPropagation();
			var OrderUID = $("#OrderUID").val();
			var WorkflowUID = $("#ReverseStatusUID").val();
			var ReversedRemarks = $("#ReversedRemarks").val();
			var DependentWorkflowModuleUIDs = $("#ReverseDependentWorkflows").val();

			var ClearChecklistData;
			if($('#ClearChecklistData').prop("checked") == true){
                ClearChecklistData = 1;
            }
            else if($('#ClearChecklistData').prop("checked") == false){
                ClearChecklistData = 0;
            }

			if (WorkflowUID == "") {
				$.notify(
					{ icon: "icon-bell-check", message: "Please select workflow" },
					{ type: "danger", delay: 2000 }
				);
				return false;
			}

			$.ajax({
				type: "POST",
				url: base_url + "OrderComplete/WorkflowOrderReverse",
				data: {
					OrderUID: OrderUID,
					WorkflowUID: WorkflowUID,
					DependentWorkflowModuleUIDs: DependentWorkflowModuleUIDs,
					ReversedRemarks: ReversedRemarks,
					ClearChecklistData: ClearChecklistData,
					ReverseInitiatedWorkflowModuleUID: Page_WorkflowModuleUID,
				},
				dataType: "json",
				cache: false,
				beforeSend: function () {},
				success: function (response) {
					if (response.success == 1) {
						$("#modal-OrderReverse").modal("hide");

						redirect_url = response["RedirectURL"] != "" ? response["RedirectURL"] : window.location.href;

						$.notify(
							{ icon: "icon-bell-check", message: response.message },
							{
								type: "success",
								delay: 2000,
								onClose: redirecturl(redirect_url),
							}
						);
					} else {
						swal({
							title: "<i class='icon-close2 icondanger'></i>",
							html: "<p>Reverse Failed</p>",
							confirmButtonClass: "btn btn-success",
							allowOutsideClick: false,
							width: "300px",
							buttonsStyling: false,
						}).catch(swal.noop);
					}
				},
				error: function (jqXHR) {
					swal({
						title: "<i class='icon-close2 icondanger'></i>",
						html: "<p>Reverse Failed</p>",
						confirmButtonClass: "btn btn-success",
						allowOutsideClick: false,
						width: "300px",
						buttonsStyling: false,
					}).catch(swal.noop);
				},
			});
		});

	$(document)
		.off("change", "#ReverseStatusUID")
		.on("change", "#ReverseStatusUID", function (e) {

			$("#ReversedRemarks").val("");
			$("#ClearChecklistData").prop("checked", false);
			$("#ReversedRemarksdiv").show();
			$("#ClearChecklistDiv").show();

			/* var OrderUID = $("#OrderUID").val();
			var WorkflowUID = $("#ReverseStatusUID").val();
			$("#ReversedRemarksdiv").hide();
			$("#ClearChecklistDiv").hide();
			$("#ReversedRemarks").val("");
			$("#ClearChecklistData").prop("checked", false);

			if (WorkflowUID == "") {
				$("#ReverseDependentWorkflowsDiv").hide();
			}

			$.ajax({
				type: "POST",
				url: base_url + "OrderComplete/get_dependentworkflow",
				data: { OrderUID: OrderUID, WorkflowUID: WorkflowUID },
				dataType: "json",
				cache: false,
				beforeSend: function () {},
				success: function (response) {
					if (response != "") {
						var DependentWorkflows = response.reduce((accumulator, value) => {
							return (
								accumulator +
								'<Option value="' +
								value.WorkflowModuleUID +
								'">' +
								value.WorkflowModuleName +
								"</Option>"
							);
						}, "");
						if (DependentWorkflows != "") {
							$("#ReverseDependentWorkflowsDiv").show();
							$("#ReverseDependentWorkflows").html(DependentWorkflows);
							$("#ReverseDependentWorkflows").trigger("change");
						}
					} else {
						$("#ReverseDependentWorkflows").html("");
						$("#ReverseDependentWorkflowsDiv").hide();
					}

					$("#ReversedRemarksdiv").show();
					$("#ClearChecklistDiv").show();
					callselect2byid("ReverseDependentWorkflows");
				},
			}); */
		});
}); //Document Ends

function zipcode_select() {
	$(".dropdown-menu a").click(function () {
		$(this)
			.closest(".dropdown")
			.find("input.select")
			.val($(this).attr("data-value"));
	});
}

var AjaxGetCustomerProjects = (() => {
	var _ref = _asyncToGenerator(function* (CustomerUID) {
		return new Promise(function (resolve, reject) {
			resolve(
				$.ajax({
					type: "POST",
					url: base_url + "CommonController/GetCustomerDetails",
					data: {
						CustomerUID: CustomerUID,
					},
					dataType: "json",
					beforeSend: function () {
						addcardspinner("#Orderentrycard");
					},
				})
			);
		});
	});

	return function AjaxGetCustomerProjects(_x) {
		return _ref.apply(this, arguments);
	};
})();

var AjaxGetCustomerProducts = (() => {
	var _ref = _asyncToGenerator(function* (CustomerUID) {
		return new Promise(function (resolve, reject) {
			resolve(
				$.ajax({
					type: "POST",
					url: base_url + "CommonController/Get_CustomerProducts",
					data: {
						CustomerUID: CustomerUID,
					},
					dataType: "json",
					beforeSend: function () {
						addcardspinner("#Orderentrycard");
					},
				})
			);
		});
	});

	return function AjaxGetCustomerProducts(_x) {
		return _ref.apply(this, arguments);
	};
})();

var SendAsyncAjaxRequest = (() => {
	var _ref2 = _asyncToGenerator(function* (
		RequestType,
		ReqeustURL,
		RequestData,
		ResponseDataType,
		processData,
		contentType,
		BeforeSend_CallBack
	) {
		var ajaxoptions = {
			type: RequestType,
			url: base_url + ReqeustURL,
			data: RequestData,
			dataType: ResponseDataType,
			cache: false,
			beforeSend: BeforeSend_CallBack,
		};

		if (processData == false) {
			ajaxoptions.processData = processData;
		}

		if (contentType == false) {
			ajaxoptions.contentType = contentType;
		}

		if (typeof this.processData === "undefined") {
			this.processData = true;
		}
		if (typeof this.contentType === "undefined") {
			this.contentType = true;
		}
		return new Promise(function (resolve, reject) {
			resolve($.ajax(ajaxoptions));
		});
	});

	return function SendAsyncAjaxRequest(_x2, _x3, _x4, _x5, _x6, _x7, _x8) {
		return _ref2.apply(this, arguments);
	};
})();

var TestSendAsyncAjaxRequest = (() => {
	var _ref3 = _asyncToGenerator(function* (
		RequestType,
		ReqeustURL,
		RequestData,
		ResponseDataType,
		processData,
		contentType,
		BeforeSend_CallBack
	) {
		var ajaxoptions = {
			type: RequestType,
			url: base_url + ReqeustURL,
			data: RequestData,
			dataType: ResponseDataType,
			cache: false,
			beforeSend: BeforeSend_CallBack,
		};

		if (processData == false) {
			ajaxoptions.processData = processData;
		}

		if (contentType == false) {
			ajaxoptions.contentType = contentType;
		}

		if (typeof this.processData === "undefined") {
			this.processData = true;
		}
		if (typeof this.contentType === "undefined") {
			this.contentType = true;
		}
		console.log(ajaxoptions);
		return new Promise(function (resolve, reject) {
			$.ajax(ajaxoptions)
				.done(function (data) {
					resolve(data);
				})
				.fail(function (error) {
					reject(error);
				});
		});
	});

	return function TestSendAsyncAjaxRequest(
		_x9,
		_x10,
		_x11,
		_x12,
		_x13,
		_x14,
		_x15
	) {
		return _ref3.apply(this, arguments);
	};
})();

var disposepopover = function (e) {
	$("[data-toggle=exception-popover]").popover("dispose");
	$("[data-toggle=clearexceptionpopover]").popover("dispose");
	$("[data-toggle=OnHold-popover]").popover("dispose");
	$("[data-toggle=Release-popover]").popover("dispose");
};

function check_is_url_contains_string_value(url) {
	// body...
	var substrings = [
			"Ordersummary",
			"ThirdPartyTeam",
			"FHA_VA_CaseTeam",
			"TitleTeam",
			"DocChase",
		],
		length = substrings.length;
	while (length--) {
		if (url.indexOf(substrings[length]) != -1) {
			// one of the substrings is in yourstring
			return true;
		}
	}
	return false;
}

function is_url_contains_string(word) {
	// body...
	var url = window.location.href;
	if (url.indexOf(word) != -1) {
		// one of the substrings is in yourstring
		return true;
	}
	return false;
}

var fn_review_complete = function (button, button_text) {
	var OrderUID = $("#OrderUID").val();

	$.ajax({
		type: "POST",
		url: base_url + "OrderComplete/ReviewComplete",
		data: {
			OrderUID: OrderUID,
		},
		dataType: "json",
		cache: false,
		beforeSend: function () {
			addcardspinner("#Orderentrycard");
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "success",
						delay: 1000,
					}
				);
				disposepopover();
				if (check_is_url_contains_string_value(window.location.href)) {
					window.location.href = base_url + "Revieworders";
				} else {
					triggerpage(base_url + "Revieworders");
				}
			} else {
				swal({
					title: "<i class='icon-close2 icondanger'></i>",
					html: "<p>" + data.message + "</p>",
					confirmButtonClass: "btn btn-success",
					allowOutsideClick: false,
					width: "300px",
					buttonsStyling: false,
				}).catch(swal.noop);
			}
			removecardspinner("#Orderentrycard");
			$(button).prop("disabled", false);
			$(button).html(button_text);
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
		},
	});
};

var fn_raise_exception = function (formdata, button, button_text) {
	var OrderUID = $("#OrderUID").val();

	$.ajax({
		type: "POST",
		url: base_url + "OrderComplete/RaiseException",
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			button.attr("disabled", true);
			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "success",
						delay: 1000,
					}
				);
				disposepopover();

				if (check_is_url_contains_string_value(window.location.href)) {
					window.location.reload();
				} else {
					triggerpage(window.location.href);
				}
			} else {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
			}
			button.html(button_text);
			button.attr("disabled", false);
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
			button.html(button_text);
			button.attr("disabled", false);
		},
	});
};

var fn_clear_exception = function (formdata, button, button_text) {
	var OrderUID = $("#OrderUID").val();
	$.ajax({
		type: "POST",
		url: base_url + "OrderComplete/ClearException",
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			// button.attr("disabled", true);
			// $submitbuttons.attr("disabled", true);
			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/

				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "success",
						delay: 1000,
					}
				);
				disposepopover();
				if (check_is_url_contains_string_value(window.location.href)) {
					window.location.reload();
				} else {
					triggerpage(window.location.href);
				}
				$("#ClearException").modal("hide");
			} else if (data.validation_error == 2) {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 4000,
					}
				);
			} else {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
			}
			button.html(button_text);
			$(button).prop("disabled", false);

			// button.attr("disabled", false);
			// $submitbuttons.attr("disabled", false);
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
		},
	});
};

var fn_raise_docchase = function (formdata, button, button_text) {
	var OrderUID = $("#OrderUID").val();

	$.ajax({
		type: "POST",
		url: base_url + "OrderComplete/RaiseDocchase",
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			button.attr("disabled", true);
			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/
				$.notify(
					{ icon: "icon-bell-check", message: data["message"] },
					{
						type: "success",
						delay: 2000,
						onClose: redirecturl(window.location.href),
					}
				);
				disposepopover();
				$("#RaiseDocChase").modal("hide");
			} else {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
				button.html(button_text);
				button.attr("disabled", false);
			}
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
			button.html(button_text);
			button.attr("disabled", false);
		},
	});
};

var fn_clear_docchase = function (formdata, button, button_text) {
	$("#completemultipledocchase_div").html("");

	var OrderUID = $("#OrderUID").val();
	$.ajax({
		type: "POST",
		url: base_url + "OrderComplete/ClearDocChase",
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			// button.attr("disabled", true);
			// $submitbuttons.attr("disabled", true);
			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/
				$.notify(
					{ icon: "icon-bell-check", message: data["message"] },
					{
						type: "success",
						delay: 2000,
						onClose: redirecturl(window.location.href),
					}
				);
				disposepopover();
				$("#ClearDocChase").modal("hide");
				$("#completemultipledocchase_div").html("");
				$("#modal-completemultipledocchase").modal("hide");
			} else if (data.validation_error == 2) {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
				button.html(button_text);
				$(button).prop("disabled", false);
				$("#ClearDocChase").modal("hide");
				$("#modal-completemultipledocchase").modal("show");
				$("#completemultipledocchase_div").html(data.html);
			} else {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
				button.html(button_text);
				$(button).prop("disabled", false);
			}

			// button.attr("disabled", false);
			// $submitbuttons.attr("disabled", false);
		},
		error: function (jqXHR) {
			button.html(button_text);
			$(button).prop("disabled", false);
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
		},
	});
};

var fn_clear_multipledocchase = function (formdata, button, button_text) {
	var OrderUID = $("#OrderUID").val();
	$.ajax({
		type: "POST",
		url: base_url + "OrderComplete/ClearMultipleDocChase",
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			// button.attr("disabled", true);
			// $submitbuttons.attr("disabled", true);
			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/

				$.notify(
					{ icon: "icon-bell-check", message: data["message"] },
					{
						type: "success",
						delay: 2000,
						onClose: redirecturl(window.location.href),
					}
				);
				disposepopover();
				$("#ClearDocChase").modal("hide");
				$("#completemultipledocchase_div").html("");
				$("#modal-completemultipledocchase").modal("hide");
			} else {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
				button.html(button_text);
				$(button).prop("disabled", false);
			}

			// button.attr("disabled", false);
			// $submitbuttons.attr("disabled", false);
		},
		error: function (jqXHR) {
			button.html(button_text);
			$(button).prop("disabled", false);
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
		},
	});
};

/*^^^^ Document CheckIn Complete Starts ^^^^^*/

$(document)
	.off("click", "#DocumentCheckIncomplete")
	.on("click", "#DocumentCheckIncomplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		/*SWEET ALERT CONFIRMATION*/
		swal({
			title:
				'<div class="text-primary" id="iconchg"><i style="font-size: 40px;" class="fa fa-info-circle fa-5x"></i></div>',
			html:
				'<span id="modal_msg" class= "modal_spanheading" > Do you want to complete Doc Check-In ?</span>',
			showCancelButton: true,
			confirmButtonClass: "btn btn-success",
			cancelButtonClass: "btn btn-danger",
			buttonsStyling: false,
			closeOnClickOutside: false,
			allowOutsideClick: false,
			showLoaderOnConfirm: true,
			position: "bottom-middle",
		}).then(
			function (confirm) {
				$('a[data-dismiss="alert"]').trigger("click");
				var OrderUID = $("#OrderUID").val();

				$.ajax({
					type: "POST",
					url: base_url + "OrderComplete/DocumentCheckInComplete",
					data: {
						OrderUID: OrderUID,
					},
					dataType: "json",
					cache: false,
					beforeSend: function () {
						addcardspinner("#Orderentrycard");
						$(button).html(
							'<i class="fa fa-spin fa-spinner"></i> Completing...'
						);
						$(button).prop("disabled", true);
					},
					success: function (data) {
						if (data.validation_error == 0) {
							/*Sweet Alert MSG*/
							$.notify(
								{
									icon: "icon-bell-check",
									message: data["message"],
								},
								{
									type: "success",
									delay: 1000,
								}
							);
							disposepopover();
							if (check_is_url_contains_string_value(window.location.href)) {
								window.location.href = base_url + "DocumentCheckInOrders";
							} else {
								triggerpage(base_url + "DocumentCheckInOrders");
							}
						} else {
							swal({
								text: "<p>" + data.message + "</p>",
								type: "warning",
								confirmButtonText: "Ok",
								confirmButtonClass: "btn btn-success",
								timer: 3000,
								buttonsStyling: false,
							}).catch(swal.noop);
						}
						$(button).prop("disabled", false);
						$(button).html(button_text);
					},
				}).always(function () {
					$(button).html(button_text);
					$(button).prop("disabled", false);
				});
			},
			function (dismiss) {
				swal({
					title: "Cancelled",
					timer: 1000,
					type: "error",
					confirmButtonClass: "btn btn-info",
					buttonsStyling: false,
				}).catch(swal.noop);
			}
		);
	});
/*^^^^ Document CheckIn Complete Ends ^^^^^*/

function SwalConfirmExport(
	OrderUID,
	OrderNumber,
	currentrow,
	table,
	LoanNumber
) {
	swal({
		title: "Export Document",
		html:
			`
		<div class="text-left ml-20" style="font-size:16px;">
		<div class="form-check">
		<label class="form-check-label">
		<input id="export1" type="checkbox" class="form-check-input exportformat" value="1" checked> Export as Single PDF
		<span class="form-check-sign">
		<span class="check"></span>
		</span>
		</label>
		</div>` +
			`<div class="form-check">
		<label class="form-check-label">
		<input id="export2" type="checkbox" class="form-check-input exportformat" value="2"> Export as Zip
		<span class="form-check-sign">
		<span class="check"></span>
		</span>
		</label>
		</div>
		</div>`,
		showCancelButton: true,
		confirmButtonClass: "btn btn-success",
		cancelButtonClass: "btn btn-danger",
		confirmButtonText: "Export",
		cancelButtonText: "cancel please!",
		closeOnConfirm: false,
		closeOnCancel: true,
		showLoaderOnConfirm: true,
		buttonsStyling: false,
		preConfirm: function () {
			return new Promise(function (resolve, reject) {
				if ($("#export1").prop("checked") || $("#export2").prop("checked")) {
					var responseobj = {
						SinglePDF: $("#export1").prop("checked"),
						ZipFile: $("#export2").prop("checked"),
					};
					var fn_array = [];
					if (responseobj.SinglePDF) {
						fn_array[0] = ExportPDF(OrderUID, OrderNumber, LoanNumber);
					}
					if (responseobj.ZipFile) {
						fn_array[1] = ExportZip(OrderUID, OrderNumber, LoanNumber);
					}
					Promise.race(fn_array).then(function (response) {
						resolve("success");
						if (location.href == base_url + "Export") {
							triggerpage(base_url + "Export");
						}
						// table
						// 	.row(currentrow)
						// 	.remove()
						// 	.draw();
					});
				} else {
					reject("Nothing Choosen");
				}
			});
		},
		onOpen: function () {
			$("#export1").focus();
		},
	})
		.then(function (result) {
			// swal(JSON.stringify(result))
		})
		.catch(swal.noop);
}

function ExportPDF(OrderUID, OrderNumber, LoanNumber) {
	return new Promise(function (resolve, reject) {
		var clickable_link = document.createElement("a");
		clickable_link.href = base_url + "Indexing/DownloadPDF/" + OrderUID;
		clickable_link.target = "_blank";
		clickable_link.download =
			(LoanNumber ? LoanNumber : OrderNumber) + "_Export.pdf";
		clickable_link.click();
		resolve("Success");

		/*^^^^^ OR USE Below ^^^^*/
	});
}

function ExportZip(OrderUID, OrderNumber, LoanNumber) {
	return new Promise(function (resolve, reject) {
		var clickable_link = document.createElement("a");
		clickable_link.href = base_url + "Indexing/DownloadZip/" + OrderUID;
		clickable_link.target = "_blank";
		clickable_link.download =
			(LoanNumber ? LoanNumber : OrderNumber) + "_Export.zip";
		clickable_link.click();
		resolve("Success");
	});
}

function toArray() {
	var pages = $("ol.sortable .leaf");
	var JSONArray = [];
	$(pages).each(function (key, elem) {
		var dataobject = {};

		var documenttype = findParentElement($(elem)[0], "li");
		var category = findParentElement($(documenttype)[0], "li");

		dataobject.CategoryName = $(category).attr("data-category");
		dataobject.DocumentTypeName = $(documenttype).attr("data-documenttype");
		dataobject.Page = $(elem).attr("data-category");
		JSONArray.push(dataobject);
	});

	console.table(JSONArray);
	return JSONArray;
}

function findParentElement(elem, tagName) {
	var parent = elem.parentNode;

	if (parent && parent.tagName && parent.tagName.toLowerCase() != tagName) {
		parent = findParentElement(parent, tagName);
	}
	return parent;
}
$(document)
	.off("click", "#OrderRevokeBtn")
	.on("click", "#OrderRevokeBtn", function (e) {
		var OrderUID = $("#OrderUID").val();
		swal({
			title: "Are you sure?",
			text: "You won't be able to revoke this!",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn btn-success",
			cancelButtonClass: "btn btn-danger",
			confirmButtonText: "Yes",
			buttonsStyling: false,
		})
			.then(function () {
				swal({
					title: "Revoked!",
					text: "Your order has been Revoked.",
					type: "success",
					confirmButtonClass: "btn btn-success",
					buttonsStyling: false,
				});
				$.ajax({
					type: "POST",
					url: base_url + "CommonController/CancelOrderRevoke",
					data: { OrderUID: OrderUID },
					dataType: "json",
					beforeSend: function () {},
					success: function (response) {
						if (response.validation_error == 1) {
							$.notify(
								{
									icon: "icon-bell-check",
									message: response.message,
								},
								{
									type: "success",
									delay: 1000,
								}
							);
						}
						setTimeout(function () {
							if (check_is_url_contains_string_value(window.location.href)) {
								window.location.reload();
							} else {
								triggerpage(window.location.href);
							}
						}, 2000);
					},
				});
			})
			.catch(swal.noop);
	});

$(document)
	.off("click", "#BtnOnHold")
	.on("click", "#BtnOnHold", function (e) {
		e.preventDefault();
		e.stopPropagation();
		var OrderUID = $("#OrderUID").val();
		var comments = $(".remarkstext:visible").val();
		var CustomerNotification = $("#CustomerNotification:checked").val();
		var UserEmails = $(".UserEmails").eq(1).val();
		// var Onholdtype =$('.Onholdtype option:selected').eq(1).val();

		if (comments == "") {
			$(".remarkstext").addClass("highlight-invalid");
			$.notify(
				{
					icon: "icon-bell-check",
					message: "Remarks Mandatory",
				},
				{
					type: "danger",
					delay: 1000,
				}
			);
			return false;
		}

		if (CustomerNotification == "on") {
			if (UserEmails == "" || UserEmails == "[]") {
				$.notify(
					{
						icon: "icon-bell-check",
						message: "Enter Valid Email",
					},
					{
						type: "info",
						delay: 1000,
					}
				);
				return false;
			}
		}

		var button = $(".BtnOnHold");
		var button_text = $(".BtnOnHold").html();

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...OnHolding');

		// var formdata = new FormData($(this)[0]);
		// formdata.append('OrderUID', OrderUID);
		// formdata.append('CustomerEmail', CustomerEmail);

		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/OrderOnHold",
			data: {
				OrderUID: OrderUID,
				comments: comments,
				CustomerNotification: CustomerNotification,
				UserEmails: UserEmails,
			},
			dataType: "json",
			cache: false,
			// processData: false,
			// contentType: false,
			beforeSend: function () {
				button.attr("disabled", true);
				button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
			},
			success: function (response) {
				console.log(response);
				if (response.validation_error == 1) {
					{
						$.notify(
							{
								icon: "icon-bell-check",
								message: response.message,
							},
							{
								type: "success",
								delay: 1000,
							}
						);
					}
				}
				disposepopover();
				setTimeout(function () {
					location.reload();
				}, 500);
			},
		});
	});

$(document)
	.off("click", "#BtnReleaseOnHold")
	.on("click", "#BtnReleaseOnHold", function (e) {
		var OrderUID = $("#OrderUID").val();
		var OnHoldUID = $("#OnHoldUID").val();

		var button = $(".BtnReleaseOnHold");
		var button_text = $(".BtnReleaseOnHold").html();

		var comments = $(".comments_text:visible").val();
		var CustomerNotification = $("#CustomerNotification:checked").val();
		var UserEmails = $(".ClearUserEmails").eq(1).val();

		if (comments == "") {
			$(".comments_text").addClass("highlight-invalid");
			$.notify(
				{
					icon: "icon-bell-check",
					message: "Comments Mandatory",
				},
				{
					type: "danger",
					delay: 1000,
				}
			);
			return false;
		}

		if (CustomerNotification == "on") {
			if (UserEmails == "" || UserEmails == "[]") {
				$.notify(
					{
						icon: "icon-bell-check",
						message: "Enter Valid Email",
					},
					{
						type: "info",
						delay: 1000,
					}
				);
				return false;
			}
		}

		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/ReleaseOnHold",
			data: {
				OrderUID: OrderUID,
				OnHoldUID: OnHoldUID,
				comments: comments,
				CustomerNotification: CustomerNotification,
				UserEmails: UserEmails,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				button.attr("disabled", true);
				button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
			},
			success: function (response) {
				console.log(response);
				if (response.validation_error == 1) {
					{
						$.notify(
							{
								icon: "icon-bell-check",
								message: response.message,
							},
							{
								type: "success",
								delay: 1000,
							}
						);
					}
				}
				disposepopover();
				setTimeout(function () {
					location.reload();
				}, 500);
			},
		});
	});

/*######## Orderentry and ordersummary form submit starts #########*/

/*For single entry*/

$(document)
	.off("click", ".single_submit")
	.on("click", ".single_submit", function (e) {
		OrderEntryBtnID = $(this).attr("id");
	});

$(document)
	.off("submit", "#order_frm")
	.on("submit", "#order_frm", function (event) {
		/* Act on the event */
		event.preventDefault();
		event.stopPropagation();
		button = $(".single_submit[clicked=true]");
		button_val = $(".single_submit[clicked=true]").val();
		button_text = $(".single_submit[clicked=true]").html();

		var progress = $("#orderentry-progressupload .progress-bar");

		var formData = new FormData($(this)[0]);

		$.ajax({
			type: "POST",
			url: base_url + "Orderentry/insert",
			data: formData,
			dataType: "json",
			cache: false,
			processData: false,
			contentType: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				button.attr("disabled", true);
				button.html("Loading ...");
			},
			success: function (data) {
				if (data["validation_error"] == 0) {
					if (OrderEntryBtnID == "saveandnew") {
						$.notify(
							{ icon: "icon-bell-check", message: data["message"] },
							{
								type: "success",
								delay: 2000,
								onClose: redirecturl(base_url + "Orderentry"),
							}
						);
					} else {
						$.notify(
							{ icon: "icon-bell-check", message: data["message"] },
							{
								type: "success",
								delay: 2000,
								onClose: redirecturl(base_url + "Dashboard"),
							}
						);
					}
				} else if (data["validation_error"] == 1) {
					removecardspinner("#Orderentrycard");

					button.html(button_text);
					button.removeAttr("disabled");

					invalidmessage = "";
					$.each(data, function (k, v) {
						$("#" + k)
							.addClass("is-invalid")
							.closest(".form-group")
							.removeClass("has-success")
							.addClass("has-danger");
						if (k == "LoanNumber") {
							invalidmessage += " <br>" + v;
						}
					});

					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"] + invalidmessage,
						},
						{ type: "danger", delay: 3000 }
					);
				} else if (data["validation_error"] == 2) {
					removecardspinner("#Orderentrycard");
					$("#duplicate-modal").modal("show");
					$("#Skip_duplicate").val(1);
					$("#button_value").val(button_val);
					$("#insert_html").html(data["html"]);
					$("#insert_order").removeAttr("disabled");
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(errorThrown);
			},
			failure: function (jqXHR, textStatus, errorThrown) {
				console.log(errorThrown);
			},
		});
	});

/*For ordersummary*/
$(document)
	.off("submit", "#frmordersummary")
	.on("submit", "#frmordersummary", function (event) {
		/* Act on the event */
		event.preventDefault();
		event.stopPropagation();
		button = $(".checklist_update");
		button_val = $(".checklist_update").val();
		button_text = $(".checklist_update").html();
		var OrderUID = $("#OrderUID").val();
		var progress = $(".progress-bar");
		$("#DocumentUpload").val("");
		var formData = new FormData($(this)[0]);
		frmSubmit(formData, button, progress, OrderUID, button_text);
	});

function frmSubmit(formData, button, progress, OrderUID, button_text) {
	var init_url = window.location.href;
	$.ajax({
		type: "POST",
		url: base_url + "Ordersummary/insert",
		data: formData,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			addcardspinner("#Orderentrycard");
			button.attr("disabled", true);
			button.html("Loading ...");
		},
		success: function (data) {
			if (data["validation_error"] == 0) {
				$.notify(
					{ icon: "icon-bell-check", message: data["message"] },
					{
						type: "success",
						delay: 1000,
						onClose: redirecturl(window.location.href),
					}
				);

				removecardspinner("#Orderentrycard");

				$("#orderentry-progressupload").hide();

				var aFileSubmit = [];
				var files = [];
				var respOrderUIDs = data["id"];

				Promise.all(aFileSubmit).then(function (response) {
					setTimeout(function () {
						window.location.reload();
					}, 3000);
				});
			} else if (data["validation_error"] == 1) {
				removecardspinner("#Orderentrycard");

				invalidmessage = "";
				$.each(data, function (k, v) {
					$("#" + k)
						.addClass("is-invalid")
						.closest(".form-group")
						.removeClass("has-success")
						.addClass("has-danger");
					$("#" + k + ".select2picker")
						.next()
						.find("span.select2-selection")
						.addClass("errordisplay");
					if (k == "LoanNumber") {
						invalidmessage += " <br>" + v;
					}
				});
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"] + invalidmessage,
					},
					{ type: "danger", delay: 1000 }
				);
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
		},
		failure: function (jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
		},
	}).always(function () {
		button.html(button_text);
		button.removeAttr("disabled");
	});
}

/*######## Orderentry and ordersummary form submit ends #########*/

/*######## Single Order Entry File UPload ajax starts #####*/

function SendFileAsync(formdata, filename) {
	return new Promise(function (resolve, reject) {
		if ($("#uploadPane-Card").hasClass("hide")) {
			$("#uploadPane-Card").removeClass("hide");
		}
		hash = Date.now();
		li_element =
			'<li data-hash="' +
			hash +
			'" style="list-style-type: none;"><span class="up_status fa fa-spin fa-spinner" aria-hidden="true"></span> ' +
			filename +
			'<span class="filesize pull-right" data-filename="' +
			filename +
			'"><span hash="' +
			hash +
			'" class="pma_drop_file_status" task="info"><span class="underline">Uploading...</span></span></span><br><progress max="100" value="10"></progress><span class="upload-percent"></span></li>';

		$("#uploadPane-CardBody").append(li_element);
		var progress = $("#uploadPane-CardBody").find('[data-hash="' + hash + '"]');
		$.ajax({
			url: base_url + "Orderentry/uploadfile",
			type: "POST",
			dataType: "json",
			data: formdata,
			contentType: false,
			processData: false,
			beforeSend: function () {
				console.log("send");
			},
			xhr: function () {
				xhr = new window.XMLHttpRequest();

				xhr.upload.addEventListener(
					"progress",
					function (evt) {
						if (evt.lengthComputable) {
							var percentComplete = evt.loaded / evt.total;
							percentComplete = parseInt(percentComplete * 100);
							console.log(percentComplete);
							$(progress).find("progress").val(percentComplete);
							$(progress)
								.find(".upload-percent")
								.html(percentComplete + "%");
						}
					},
					false
				);
				return xhr;
			},
		})
			.done(function (response) {
				$(progress).find(".underline").html("Success");
				$(progress).find(".up_status").removeClass("fa-spin");
				$(progress).find(".up_status").removeClass("fa-spinner");
				$(progress).find(".up_status").addClass("fa-check");
				$(progress).find(".up_status").addClass("text-success");
				$(progress).find("progress").hide();
				$(progress).find(".upload-percent").hide();
				resolve("Success");
				console.log("success");
			})
			.fail(function (jqXHR) {
				$(progress).find(".underline").html("Failed");
				$(progress).find(".up_status").removeClass("fa-spin");
				$(progress).find(".up_status").removeClass("fa-spinner");
				$(progress).find(".up_status").addClass("fa-exclamation-circle");
				$(progress).find(".up_status").addClass("text-danger");
				$(progress).find("progress").hide();
				$(progress).find(".upload-percent").hide();

				console.log(jqXHR);
				reject("error");
			})
			.always(function () {
				$(progress).find("progress").hide();
				console.log("complete");
			});
	});
}
/*######## Single Order Entry File UPload ajax ends #####*/

// Raise Follow up for document missing orders in bulk entry.
function RaiseFollowForOrders(_Orders) {
	return new Promise(function (resolve, reject) {
		$.ajax({
			url: "Orderentry/RaiseFollowUp",
			type: "POST",
			dataType: "json",
			data: { Orders: _Orders },
		})
			.done(function (response) {
				if (response.validation_error == 0) {
					console.log("Follow Up Raised Successfully");
				} else {
					console.log("Unable to Raise Follow Up");
				}
			})
			.fail(function () {
				console.log("Error", "Unable to Raise Follow Up");
			})
			.always(function () {
				console.log("complete");
			});
	});
}

function MergeFilesLater() {
	return new Promise(function (resolve, reject) {
		$.ajax({
			url: "Orderentry/MergeFilesLater",
			type: "POST",
			dataType: "json",
		})
			.done(function (response) {
				if (response.success == 0) {
					console.log(response.messge);
				} else {
					console.log(response.messge);
				}
			})
			.fail(function (jqXHR) {
				console.log("Error", "Unable to Raise Follow Up");
				console.log(jqXHR);
			})
			.always(function () {
				console.log("complete");
			});
	});
}

function Fetch_Product_DocType(object) {
	$.ajax({
		type: "POST",
		url: base_url + "CommonController/GetCustomerSubProducts",
		data: object,
		dataType: "json",
		beforeSend: function () {
			addcardspinner("#Orderentrycard");
		},

		success: function (response) {
			var ProjectCustomer = response.ProjectCustomer;

			Project_select = ProjectCustomer.reduce((accumulator, value) => {
				return (
					accumulator +
					'<Option value="' +
					value.ProjectUID +
					'">' +
					value.ProjectName +
					"</Option>"
				);
			}, "");

			$("#Single-ProjectUID").html(Project_select);

			$("#Single-ProjectUID")
				.val($("#Single-ProjectUID").find("option:first").val())
				.trigger("change");

			callselect2();
			if ($("#Single-ProjectUID").hasClass("is-invalid")) {
				$("#Single-ProjectUID")
					.removeClass("is-invalid")
					.closest(".form-group")
					.removeClass("has-danger");
			}
			removecardspinner("#Orderentrycard");
		},
	});
}
//make followup start
$(document)
	.off("click", "#BtnFollowup")
	.on("click", "#BtnFollowup", function (e) {
		e.preventDefault();
		var OrderUID = $("#OrderUID").val();
		var comments = $(".popover").find(".comments").val();
		button = $(this);
		button_val = $(this).val();
		button_text = $(this).html();
		$(this).attr("disabled", true);
		$.ajax({
			type: "POST",
			url: base_url + "Followup/audit_faild_followup",
			data: {
				OrderUID: OrderUID,
				comments: comments,
			},
			success: function (data) {
				if (data.validation_error == 0) {
					button.html("Submit");
					button.removeAttr("disabled");
					disposepopover();
					jQuery(".close").trigger("click");

					swal({
						title: "Success",
						text: "Followup Started",
						type: "success",
						timer: 5000,
						confirmButtonText: "ok",
					});
					window.location = base_url + "Followup";
				} else {
					swal({
						title: "<i class='icon-close2 icondanger'></i>",
						html: "<p>" + data.message + "</p>",
						confirmButtonClass: "btn btn-success",
						allowOutsideClick: false,
						width: "300px",
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				button.html(button_text);
				button.attr("disabled", false);
			},
			error: function (jqXHR) {
				swal({
					title: "<i class='icon-close2 icondanger'></i>",
					html: "<p>Failed to Complete</p>",
					confirmButtonClass: "btn btn-success",
					allowOutsideClick: false,
					width: "300px",
					buttonsStyling: false,
				}).catch(swal.noop);
				button.html(button_text);
				button.attr("disabled", false);
			},
		});
	});

//followup complete button click event

$(document)
	.off("click", "#BtnFollowupComplete")
	.on("click", "#BtnFollowupComplete", function (e) {
		e.preventDefault();
		var OrderUID = $("#OrderUID").val();
		var Followuptype = $(".popover")
			.find(".Followuptype")
			.children("option:selected")
			.val();
		var comments = $(".popover").find(".completecomments").val();
		$(this).attr("disabled", true);
		$.ajax({
			type: "POST",
			url: base_url + "Followup/CompleteFollowup",
			data: {
				OrderUID: OrderUID,
				Followuptype: Followuptype,
				Comments: comments,
			},
			success: function (data) {
				if (data == 1) {
					swal({
						title: "Success",
						text: "Followup Completed",
						type: "success",
						timer: 5000,
						confirmButtonText: "ok",
					});
					window.location = base_url + "DocumentCheckInOrders";
				} else {
					swal({
						title: "Failed",
						text: "Followup Not Completed",
						type: "error",
						timer: 1000,
					});
				}
			},
		});
	});

// Check is hex color

function isColor(strColor) {
	var regex = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i;
	if (regex.test(strColor)) {
		return true;
	}
	return false;
}

//prescreen assign and workflow complete
Checked_Workflows = [];
$(document)
	.off("click", "#btnprescreenassign")
	.on("click", "#btnprescreenassign", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		$(
			"input:checkbox[name=WorkflowModuleUIDToAssign]:checked:visible:visible"
		).each(function () {
			Checked_Workflows.push($(this).val());
		});
		var formData = new FormData($("#frmordersummary")[0]);
		AutoUpdate(formData);
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/PreScreenassign_complete",
			data: {
				OrderUID: OrderUID,
				Checked_Workflows: Checked_Workflows,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					$("#modal-OrderAssign").modal("hide");
					disposepopover();
					swal({
						text: "REDIRECT TO PRE-SCREEN LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "PreScreen_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});

$(document)
	.off("click", "#welcomecallComplete")
	.on("click", "#welcomecallComplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		var formData = new FormData($("#frmordersummary")[0]);
		AutoUpdate(formData);
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/welcomecall_complete",
			data: {
				OrderUID: OrderUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();
					swal({
						text: "REDIRECT TO WELCOME CALL LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "WelcomeCall_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});

$(document)
	.off("click", "#titleteamComplete")
	.on("click", "#titleteamComplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		var formData = new FormData($("#frmordersummary")[0]);
		AutoUpdate(formData);
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/titleteamComplete",
			data: {
				OrderUID: OrderUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();
					swal({
						text: "REDIRECT TO TITLE LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "TitleTeam_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});

$(document)
	.off("click", "#fhavacaseteamComplete")
	.on("click", "#fhavacaseteamComplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		var formData = new FormData($("#frmordersummary")[0]);
		AutoUpdate(formData);
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/fhavacaseteamComplete",
			data: {
				OrderUID: OrderUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();
					swal({
						text: "REDIRECT TO FHA/VA LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "FHAVACaseTeam_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});

$(document)
	.off("click", "#thirdpartyteamComplete")
	.on("click", "#thirdpartyteamComplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		var formData = new FormData($("#frmordersummary")[0]);
		AutoUpdate(formData);
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/thirdpartyteamComplete",
			data: {
				OrderUID: OrderUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();
					swal({
						text: "REDIRECT TO THIRD PARTY LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "ThirdParty_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});

/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
/** Monday 09 March 2020 **/
/** HOI PAYOFF WORKFLOW ADD **/
$(document)
	.off("click", "#hoiComplete")
	.on("click", "#hoiComplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		var formData = new FormData($("#frmordersummary")[0]);
		AutoUpdate(formData);
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/hoiComplete",
			data: {
				OrderUID: OrderUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();
					swal({
						text: "REDIRECT TO HOI LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "HOI_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});

/** Borrower Doc WORKFLOW ADD **/
$(document)
	.off("click", "#BorrowerDocComplete")
	.on("click", "#BorrowerDocComplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		var formData = new FormData($("#frmordersummary")[0]);
		AutoUpdate(formData);
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/BorrowerDocComplete",
			data: {
				OrderUID: OrderUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();
					swal({
						text: "REDIRECT TO Borrower Doc LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "BorrowerDoc_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});

/** @author Praveen Kumar <praveen.kumar@avanzegroup.com> **/
/** Monday 09 March 2020 **/
/** HOI PAYOFF WORKFLOW ADD **/
$(document)
	.off("click", "#payoffComplete")
	.on("click", "#payoffComplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		var formData = new FormData($("#frmordersummary")[0]);
		AutoUpdate(formData);
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/payoffComplete",
			data: {
				OrderUID: OrderUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();
					swal({
						text: "REDIRECT TO PAYOFF LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "PayOff_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});

$(document)
	.off("click", "#workupcomplete")
	.on("click", "#workupcomplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/workupcomplete",
			data: {
				OrderUID: OrderUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();
					swal({
						text: "REDIRECT TO WORKUP LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "WorkUp_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});
$(document)
	.off("click", "#underwritercomplete")
	.on("click", "#underwritercomplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/underwritercomplete",
			data: {
				OrderUID: OrderUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();
					swal({
						text: "REDIRECT TO UNDERWRITER LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "UnderWriter_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});
$(document)
	.off("click", "#schedulingcomplete")
	.on("click", "#schedulingcomplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/schedulingcomplete",
			data: {
				OrderUID: OrderUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();
					swal({
						text: "REDIRECT TO SCHEDULING LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "Scheduling_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});
$(document)
	.off("click", "#closingcomplete")
	.on("click", "#closingcomplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();

		var OrderUID = $("#OrderUID").val();
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/closingcomplete",
			data: {
				OrderUID: OrderUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();
					swal({
						text: "REDIRECT TO CLOSING LIST?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
						function () {
							triggerpage(base_url + "Closing_Orders");
						},
						function (dismiss) {
							triggerpage(window.location.href);
						}
					);
				} else {
					swal({
						text: "<p>" + data.message + "</p>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						timer: 3000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		});
	});

$(document)
	.off("click", ".btnWorkflowComplete")
	.on("click", ".btnWorkflowComplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();
		Checked_Workflows = [];
		$("input:checkbox[name=WorkflowModuleUIDToAssign]:checked:visible").each(
			function () {
				Checked_Workflows.push($(this).val());
			}
		);

		var OrderUID = $("#OrderUID").val();
		var formData = new FormData($("#frmordersummary")[0]);
		var WorkflowModuleUID = $(this).attr("data-WorkflowModuleUID");
		var WorkflowModuleName = $(this).attr("data-WorkflowModuleName");
		var skipdependent = $(this).attr("data-skipdependent");
		var nbsmodalconfirmation = $(this).attr("data-nbsmodalconfirmation");
		AutoUpdate(formData);
		$.when(AutoUpdate(formData)).then(
			$.ajax({
				type: "POST",
				url: base_url + "OrderComplete/workflow_complete",
				data: {
					OrderUID: OrderUID,
					WorkflowModuleUID: WorkflowModuleUID,
					Checked_Workflows: Checked_Workflows,
					skipdependent: skipdependent,
					nbsmodalconfirmation: nbsmodalconfirmation,
				},
				dataType: "json",
				cache: false,
				beforeSend: function () {
					addcardspinner("#Orderentrycard");
					$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
					$(button).prop("disabled", true);
				},
				success: function (data) {
					if (data.validation_error == 0) {
						/*Sweet Alert MSG*/
						$.notify(
							{
								icon: "icon-bell-check",
								message: data["message"],
							},
							{
								type: "success",
								delay: 1000,
							}
						);
						$("#modal-OrderAssign").modal("hide");

						disposepopover();
						if (data.popup_message && data.popup_message != "") {
							swal({
								text: "REDIRECT TO " + data.popup_message + "?",
								type: "warning",
								showCancelButton: true,
								confirmButtonText: "Yes, Redirect!",
								cancelButtonText: "No, Stay here",
								confirmButtonClass: "btn btn-success",
								cancelButtonClass: "btn btn-info",
								buttonsStyling: false,
							}).then(
								function () {
									triggerpage(base_url + data.redirect);
								},
								function (dismiss) {
									triggerpage(window.location.href);
								}
							);
						} else {
							triggerpage(window.location.href);
						}
					} else if (data.validation_error == 2) {
						$("#modal-OrderAssign").modal("show");
						$(".modal_dependentworkflow").html("");
						$.each(data.DependentWorkflows, function (
							index,
							DependentWorkflow
						) {
							/* iterate through array or object */
							$(
								".table_modal_dependentworkflow .modal_dependentworkflow"
							).append(
								`<tr>
					<td>` +
									(index + 1) +
									`</td>
					<td>` +
									DependentWorkflow.WorkflowModuleName +
									`</td>
					<td>
					<div class="form-check">
					<label class="form-check-label">
					<input class="form-check-input" type="checkbox" id="WorkflowModuleUID` +
									DependentWorkflow.WorkflowModuleUID +
									`" value="` +
									DependentWorkflow.WorkflowModuleUID +
									`" data-incre="` +
									DependentWorkflow.WorkflowModuleUID +
									`" name="WorkflowModuleUIDToAssign"> 
					<span class="form-check-sign">
					<span class="check"></span>
					</span>

					</label>
					</div>
					</td>
					</tr>`
							);
						});
						$(".btnWorkflowComplete").attr(
							"data-WorkflowModuleUID",
							WorkflowModuleUID
						);
						$(".btnWorkflowComplete").attr(
							"data-WorkflowModuleName",
							WorkflowModuleName
						);
						$(".completeworkflowname").text(WorkflowModuleName);
					} else if(data.NBSRequiredConfirmation == 1) {
						swal({
							text: data.message,
							type: "warning",
							showCancelButton: true,
							confirmButtonText: "Yes",
							cancelButtonText: "No",
							confirmButtonClass: "btn btn-success",
							cancelButtonClass: "btn btn-info",
							buttonsStyling: false,
						}).then(
						function () {
							$('.btnWorkflowComplete').attr("data-nbsmodalconfirmation", "1");
							$(".tocompleteworkflow").trigger("click");
						},
						function (dismiss) {
							
						}
						);
					} else {
						swal({
							text: "<h5>" + data.message + "</h5>",
							type: "warning",
							confirmButtonText: "Ok",
							confirmButtonClass: "btn btn-success",
							//timer: 5000,
							buttonsStyling: false,
						}).catch(swal.noop);
					}
					$(button).prop("disabled", false);
					$(button).html(button_text);
				},
			}).always(function () {
				$(button).html(button_text);
				$(button).prop("disabled", false);
			})
		);
	});

$(document)
	.off("click", ".PickNewOrder")
	.on("click", ".PickNewOrder", function () {
		var OrderUID = $(this).attr("data-orderuid");

		var WorkflowModuleUID = $(this).attr("data-WorkflowModuleUID");

		$.ajax({
			url: "CommonController/PickExistingOrderCheck",
			type: "POST",
			dataType: "json",
			data: { OrderUID: OrderUID, WorkflowModuleUID: WorkflowModuleUID },
			beforeSend: function () {},
		})
			.done(function (response) {
				if (response.validation_error == 2) {
					$.notify(
						{
							icon: "icon-bell-check",
							message: response.message,
						},
						{
							type: response.color,
							delay: 1000,
						}
					);

					setTimeout(function () {
						triggerpage("Ordersummary/index/" + OrderUID);
					}, 3000);
				} else if (response.validation_error == 1) {
					$(".AssignmentName").html(response.message.UserName);
					$(".btnChangeOrderAssignment").attr(
						"data-OrderAssignmentUID",
						response.message.OrderAssignmentUID
					);
					$("#ChangeOrderAssignment").modal("show");
				}
			})
			.fail(function (jqXHR) {
				console.error("error", jqXHR);
				$.notify(
					{
						icon: "icon-bell-check",
						message: "unable to assign",
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
			})
			.always(function () {
				console.log("complete");
			});
	});

$(document)
	.off("click", ".btn-dismiss-ChangeOrderAssignment")
	.on("click", ".btn-dismiss-ChangeOrderAssignment", function () {
		var currentactivetabhref = $(".customtab").find(".active").attr("href");
		if (currentactivetabhref == "#orderslist") {
			location.reload(true);
		}
	});

$(document)
	.off("click", ".btncleardocchase")
	.on("click", ".btncleardocchase", function (e) {
		btncleardocchase_value = $(this).val();
		$button = $(this);
	});

$(document)
	.off("click", ".btnmultiplecleardocchase")
	.on("click", ".btnmultiplecleardocchase", function (e) {
		btnmultiplecleardocchase_value = $(this).val();
		$btnmultiplecleardocchasebutton = $(this);
	});

$(document)
	.off("click", ".btnraisemultipleescalation")
	.on("click", ".btnraisemultipleescalation", function (e) {
		btnmultipleraiseescalation_value = $(this).val();
		$btnmultipleraiseescalationbutton = $(this);
	});

$(document)
	.off("submit", "#frmRaiseEsclation")
	.on("submit", "#frmRaiseEsclation", function (e) {
		var OrderUID = $("#OrderUID").val();

		var button = $(".btnRaiseEsclation");
		var button_text = $(".btnRaiseEsclation").html();

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Raising');

		var formdata = new FormData($(this)[0]);
		formdata.append("OrderUID", OrderUID);
		formdata.append("Page", Const_Page);

		var accessurl = base_url + "OrderComplete/RaiseEsclation";
		if (Const_Page == "DocChase") {
			accessurl = base_url + "OrderComplete/RaiseDocChaseEsclation";
		}

		$.ajax({
			type: "POST",
			url: accessurl,
			data: formdata,
			dataType: "json",
			cache: false,
			processData: false,
			contentType: false,
			beforeSend: function () {
				button.attr("disabled", true);
				button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: "Esclation Raised",
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();

					if (check_is_url_contains_string_value(window.location.href)) {
						window.location.reload();
					} else {
						triggerpage(window.location.href);
					}
					$("#RaiseEsclation").modal("hide");
				} else {
					$.notify(
						{
							icon: "icon-bell-check",
							message: "Esclation Raised Failed",
						},
						{
							type: "danger",
							delay: 1000,
						}
					);
					button.html(button_text);
					button.attr("disabled", false);

					$.each(data, function (k, v) {
						console.log(k);
						$("#" + k)
							.addClass("is-invalid")
							.closest(".form-group")
							.removeClass("has-success")
							.addClass("has-danger");
						$("#" + k + ".select2picker")
							.next()
							.find("span.select2-selection")
							.addClass("errordisplay");
					});
				}
			},
			error: function (jqXHR) {
				swal({
					title: "<i class='icon-close2 icondanger'></i>",
					html: "<p>Failed to Complete</p>",
					confirmButtonClass: "btn btn-success",
					allowOutsideClick: false,
					width: "300px",
					buttonsStyling: false,
				}).catch(swal.noop);
				button.html(button_text);
				button.attr("disabled", false);
			},
		});
	});

$(document)
	.off("submit", "#frmClearEsclation")
	.on("submit", "#frmClearEsclation", function (e) {
		var OrderUID = $("#OrderUID").val();

		var button = $(".btnClearEsclation");
		var button_text = $(".btnClearEsclation").html();

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Raising');

		var formdata = new FormData($(this)[0]);
		formdata.append("OrderUID", OrderUID);
		formdata.append("Page", Const_Page);

		var accessurl = base_url + "OrderComplete/ClearEsclation";
		if (Const_Page == "DocChase") {
			accessurl = base_url + "OrderComplete/ClearDocChaseEsclation";
		}

		$.ajax({
			type: "POST",
			url: accessurl,
			data: formdata,
			dataType: "json",
			cache: false,
			processData: false,
			contentType: false,
			beforeSend: function () {
				button.attr("disabled", true);
				button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: "Esclation Cleared",
						},
						{
							type: "success",
							delay: 1000,
						}
					);
					disposepopover();

					window.location.reload();
					$("#ClearEsclation").modal("hide");
				} else {
					$.notify(
						{
							icon: "icon-bell-check",
							message: "Esclation Cleared Failed",
						},
						{
							type: "danger",
							delay: 1000,
						}
					);
					button.html(button_text);
					button.attr("disabled", false);
				}
			},
			error: function (jqXHR) {
				swal({
					title: "<i class='icon-close2 icondanger'></i>",
					html: "<p>Failed to Complete</p>",
					confirmButtonClass: "btn btn-success",
					allowOutsideClick: false,
					width: "300px",
					buttonsStyling: false,
				}).catch(swal.noop);
				button.html(button_text);
				button.attr("disabled", false);
			},
		});
	});

$(document)
	.off("submit", "#frmRaiseDocChase")
	.on("submit", "#frmRaiseDocChase", function (e) {
		e.preventDefault();
		e.stopPropagation();
		var OrderUID = $("#OrderUID").val();

		var button = $(".btnraisedocchase");
		var button_text = $(".btnraisedocchase").html();
		/*if($('#docchaseraiseReason').val() == '') {
		$.notify({
			icon: "icon-bell-check",
			message: 'Select Reason'
		}, {
			type: "danger",
			delay: 1000
		});
		return false;
	}*/

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Raising');

		var formdata = new FormData($(this)[0]);
		formdata.append("OrderUID", OrderUID);
		formdata.append("Page", Const_Page);

		fn_raise_docchase(formdata, button, button_text);
	});

$(document)
	.off("submit", "#frm_completemultipledocchase")
	.on("submit", "#frm_completemultipledocchase", function (e) {
		e.preventDefault();
		e.stopPropagation();
		var OrderUID = Const_ORDERUID;

		var button = $btnmultiplecleardocchasebutton;
		var button_text = $btnmultiplecleardocchasebutton.html();
		/*if($('#multipledocchaseclearReason').val() == '') {
		$.notify({
			icon: "icon-bell-check",
			message: 'Select Reason'
		}, {
			type: "danger",
			delay: 1000
		});
		return false;
	}*/
		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Clearing');

		var WorkflowModuleUID = $(
			".WorkflowModuleUIDClearChase_box:checked:visible"
		)
			.map(function (i, e) {
				return $(e).attr("data-WorkflowModuleUID");
			})
			.toArray();

		var formdata = new FormData($(this)[0]);
		formdata.append("OrderUID", OrderUID);
		formdata.append("submit", btnmultiplecleardocchase_value);
		formdata.append("Page", Const_Page);
		formdata.append("WorkflowModuleUID", WorkflowModuleUID);

		$.when(AutoUpdate(new FormData($("#frmordersummary")[0]))).then(
			fn_clear_multipledocchase(formdata, button, button_text)
		);
	});

$(document)
	.off("submit", "#frmclearDocChase")
	.on("submit", "#frmclearDocChase", function (e) {
		e.preventDefault();
		e.stopPropagation();
		var OrderUID = Const_ORDERUID;

		var button = $button;
		var button_text = $button.html();
		/*if($('#docchaseclearReason').val() == '') {
		$.notify({
			icon: "icon-bell-check",
			message: 'Select Reason'
		}, {
			type: "danger",
			delay: 1000
		});
		return false;
	}*/

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Clearing');

		var formdata = new FormData($(this)[0]);
		formdata.append("OrderUID", OrderUID);
		formdata.append("submit", btncleardocchase_value);
		formdata.append("Page", Const_Page);
		$.when(AutoUpdate(new FormData($("#frmordersummary")[0]))).then(
			fn_clear_docchase(formdata, button, button_text)
		);
	});

function OpenSelect2() {
	var $select2 = $(this).data("select2");
	setTimeout(function () {
		if (!$select2.opened()) {
			$select2.open();
		}
	}, 0);
}

$(document)
	.off("click", ".addchecklistrow")
	.on("click", ".addchecklistrow", function (e) {
		var workflowUId = $(this).attr("data-moduleUID");
		var count = $(this).attr("data-count");
		$(this).attr("data-count", Number(count) + 1);
		$.post(
			"CommonController/get_newchecklistrow",
			{ OrderUID: OrderUID, WorkflowModuleUID: workflowUId, count: count },
			function (result) {
				$("table.checklisttable tbody").append(result);
				$("table.checklisttable tbody").find("select.pre_select").select2();
				checklistdatepicker_init();
			}
		);
	});

$(document)
	.off("click", ".removechecklist")
	.on("click", ".removechecklist", function (e) {
		$(this).closest(".removeRow").remove();
	});
$(document)
	.off("click", ".ProblemIdentifiedbtn")
	.on("click", ".ProblemIdentifiedbtn", function (e) {
		if ($(this).attr("data-status") == "show") {
			$(".ProblemIdentified").attr("style", "display : ");
			$(this).attr("data-status", "hide");
			$(this).text("Show Issue(s)");
			$(this).attr("title", "Show Issue(s)");
		} else {
			$(".ProblemIdentified").attr("style", "display : none");
			$(this).attr("data-status", "show");
			$(this).text("Show All Checklist");
			$(this).attr("title", "Show All Checklist");
		}
	});
$(document)
	.off("focus focusout", ".checklists")
	.on("focus focusout", ".checklists", function (e) {
		if ($(this).is(":focus")) {
			$(".checklists").removeClass("highlight");
			$(this)
				.siblings(".form-check-sign")
				.children(".check")
				.removeClass("check_highlight");
			if ($(this).attr("type") == "checkbox") {
				$(this)
					.siblings(".form-check-sign")
					.children(".check")
					.addClass("check_highlight");
			} else {
				$(this).addClass("highlight");
			}
		} else {
			if ($(this).attr("type") == "checkbox") {
				$(this)
					.siblings(".form-check-sign")
					.children(".check")
					.removeClass("check_highlight");
			} else {
				$(".checklists").removeClass("highlight");
			}
		}
	});

//parking queue

$(document)
	.off("submit", "#frmRaiseParking")
	.on("submit", "#frmRaiseParking", function (e) {
		e.preventDefault();
		e.stopPropagation();
		var OrderUID = $("#OrderUID").val();

		var button = $(".btnraiseparking");
		var button_text = button.html();

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Raising');

		var formdata = new FormData($(this)[0]);
		formdata.append("OrderUID", OrderUID);
		formdata.append("Page", Const_Page);

		fn_raise_parking(formdata, button, button_text);
	});

$(document)
	.off("submit", "#frmclearParking")
	.on("submit", "#frmclearParking", function (e) {
		e.preventDefault();
		e.stopPropagation();
		var OrderUID = Const_ORDERUID;

		var button = $(".btnclearparking");
		var button_text = button.html();

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Clearing');

		var formdata = new FormData($(this)[0]);
		formdata.append("OrderUID", OrderUID);
		formdata.append("Page", Const_Page);
		fn_clear_parking(formdata, button, button_text);
	});

var fn_raise_parking = function (formdata, button, button_text) {
	var OrderUID = $("#OrderUID").val();
	var accessurl = base_url + "OrderComplete/RaiseParking";
	if (Const_Page == "DocChase") {
		accessurl = base_url + "OrderComplete/RaiseDocChaseParking";
	}

	$.ajax({
		type: "POST",
		url: accessurl,
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			button.attr("disabled", true);
			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/

				$.notify(
					{ icon: "icon-bell-check", message: data["message"] },
					{
						type: "success",
						delay: 2000,
						onClose: redirecturl(window.location.href),
					}
				);
				disposepopover();
				$("#ParkingQueue").modal("hide");
			} else {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
				button.html(button_text);
				button.attr("disabled", false);
			}
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
			button.html(button_text);
			button.attr("disabled", false);
		},
	});
};

var fn_clear_parking = function (formdata, button, button_text) {
	var OrderUID = $("#OrderUID").val();
	var accessurl = base_url + "OrderComplete/ClearParking";
	if (Const_Page == "DocChase") {
		accessurl = base_url + "OrderComplete/ClearDocChaseParking";
	}
	$.ajax({
		type: "POST",
		url: accessurl,
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			button.attr("disabled", true);
			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/

				disposepopover();
				$.notify(
					{ icon: "icon-bell-check", message: data["message"] },
					{
						type: "success",
						delay: 2000,
						onClose: redirecturl(window.location.href),
					}
				);
				$("#ClearParking").modal("hide");
			} else if (data.validation_error == 2) {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 4000,
					}
				);
			} else {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
				button.html(button_text);
				$(button).prop("disabled", false);
			}

			// button.attr("disabled", false);
			// $submitbuttons.attr("disabled", false);
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
		},
	});
};

var fn_raise_Withdrawal = function (formdata, button, button_text) {
	var OrderUID = $("#OrderUID").val();

	$.ajax({
		type: "POST",
		url: base_url + "OrderComplete/RaiseWithdrawal",
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			button.attr("disabled", true);
			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/

				$.notify(
					{ icon: "icon-bell-check", message: data["message"] },
					{
						type: "success",
						delay: 2000,
						onClose: redirecturl(window.location.href),
					}
				);
				disposepopover();

				$("#RaiseWithdrawal").modal("hide");
			} else {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
				button.html(button_text);
				button.attr("disabled", false);
			}
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
			button.html(button_text);
			button.attr("disabled", false);
		},
	});
};

var fn_clear_Withdrawal = function (formdata, button, button_text) {
	var OrderUID = $("#OrderUID").val();
	$.ajax({
		type: "POST",
		url: base_url + "OrderComplete/ClearWithdrawal",
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			button.attr("disabled", true);
			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/

				$.notify(
					{ icon: "icon-bell-check", message: data["message"] },
					{
						type: "success",
						delay: 2000,
						onClose: redirecturl(window.location.href),
					}
				);
				disposepopover();

				$("#ClearWithdrawal").modal("hide");
			} else if (data.validation_error == 2) {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 4000,
					}
				);
			} else {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
				button.html(button_text);
				$(button).prop("disabled", false);
			}
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
		},
	});
};

//withdrawal queue
$(document)
	.off("submit", "#frmraiseWithdrawal")
	.on("submit", "#frmraiseWithdrawal", function (e) {
		e.preventDefault();
		var OrderUID = $("#OrderUID").val();

		var button = $("#btnraiseWithdrawal");
		var button_text = button.html();

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Raising');

		var formdata = new FormData($(this)[0]);
		formdata.append("OrderUID", OrderUID);
		formdata.append("Page", Const_Page);
		fn_raise_Withdrawal(formdata, button, button_text);
	});

$(document)
	.off("submit", "#frmclearWithdrawal")
	.on("submit", "#frmclearWithdrawal", function (e) {
		e.preventDefault();
		var OrderUID = Const_ORDERUID;

		var button = $("#btnclearWithdrawal");
		var button_text = button.html();

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Clearing');

		var formdata = new FormData($(this)[0]);
		formdata.append("OrderUID", OrderUID);
		formdata.append("Page", Const_Page);
		fn_clear_Withdrawal(formdata, button, button_text);
	});

$(document)
	.off("click", ".btnChangeOrderAssignment")
	.on("click", ".btnChangeOrderAssignment", function (e) {
		var OrderUID = $("#OrderUID").val();

		var button = $(".btnChangeOrderAssignment");
		var button_text = $(".btnChangeOrderAssignment").html();

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Raising');

		var OrderAssignmentUID = $(this).attr("data-OrderAssignmentUID");
		$.ajax({
			type: "POST",
			url: base_url + "CommonController/ChangeOrderAssignment",
			data: { OrderAssignmentUID: OrderAssignmentUID },
			dataType: "json",
			beforeSend: function () {
				button.attr("disabled", true);
				button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/

					$.notify(
						{ icon: "icon-bell-check", message: data["message"] },
						{
							type: "success",
							delay: 2000,
							onClose: redirecturl(window.location.href),
						}
					);
					//disposepopover();

					$("#ChangeOrderAssignment").modal("hide");
				} else {
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "danger",
							delay: 1000,
						}
					);
					button.html(button_text);
					button.attr("disabled", false);
				}
			},
			error: function (jqXHR) {
				swal({
					title: "<i class='icon-close2 icondanger'></i>",
					html: "<p>Failed to change</p>",
					confirmButtonClass: "btn btn-success",
					allowOutsideClick: false,
					width: "300px",
					buttonsStyling: false,
				}).catch(swal.noop);
				button.html(button_text);
				button.attr("disabled", false);
			},
		});
	});

$(document)
	.off("click", ".sendCommand")
	.on("click", ".sendCommand", function (e) {
		e.preventDefault();
		var OrderUID = $("#tNotes-OrderUID").val();
		var WorkflowModuleUID = $("#tNotes-WorkflowModuleUID").val();
		var Commands = $("#Commands").val();
		var comments_val = $.trim(Commands);
		if (comments_val != "") {
			$.ajax({
				type: "post",
				url: base_url + "CommonController/AddCommands",
				data: {
					OrderUID: OrderUID,
					WorkflowModuleUID: WorkflowModuleUID,
					Commands: Commands,
				},
				success: function (data) {
					$(".commandBox").val("");
					data = JSON.parse(data);
					if (data) {
						$.notify(
							{
								message: "Comment updated",
							},
							{
								type: "success",
								delay: 1000,
							}
						);
						console.log(data);
						$(".CommandsappendTable").prepend(data);
					} else {
						$.notify(
							{
								message: "Comment Updation Failed",
							},
							{
								type: "danger",
								delay: 1000,
							}
						);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
				},
				failure: function (jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
				},
			});
		} else {
			$.notify(
				{ message: "Comment Required" },
				{ type: "danger", delay: 1000 }
			);
		}
	});

//change event for .meetingfield
$(document)
	.off("change", ".meetingfield")
	.on("change", ".meetingfield", function (e) {
		e.preventDefault();

		var meetingobj = {};
		$("input.meetingfield, select.meetingfield").each(function (key, elem) {
			meetingobj[$(elem).attr("name")] = $(elem).val();
		});
		meetingobj["WorkflowModuleUID"] = $("#WorkflowModuleUID").val();
		meetingobj["MeetingOrderUID"] = $("#OrderUID").val();
		console.log(meetingobj);

		if (
			meetingobj["WorkflowModuleUID"] &&
			meetingobj["MeetingOrderUID"] &&
			meetingobj["PreferedTime"] &&
			meetingobj["PreferedTimeZone"] &&
			meetingobj.PreferedTime.match(
				/((1[0-2]|0?[1-9]):([0-5][0-9]) ([AaPp][Mm]))/g
			)
		) {
			$.ajax({
				url: "CommonController/SaveMeeting",
				type: "POST",
				dataType: "json",
				data: meetingobj,
				beforeSend: function () {},
			})
				.done(function (response) {
					console.log("success", response);
				})
				.fail(function (jqXHR) {
					console.error("error", jqXHR);
				})
				.always(function () {
					console.log("complete");
				});
		}
	});

// setInterval(function(event){
// 	var init_url = window.location.href;
// 	url = $("#url").val();
// 	var formData = new FormData($("#frmordersummary")[0]);
// 	if (url == 'PreScreen' || url=='WelcomeCall' || url=='TitleTeam' || url=='FHA_VA_CaseTeam' || url=='ThirdPartyTeam') {
// 		AutoUpdate(formData);
// 	}
//  }, callUpdateTime);

function AutoUpdate(formData) {
	$.ajax({
		type: "POST",
		url: base_url + "Ordersummary/insert",
		data: formData,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		success: function (data) {
			console.log("Updated");
		},
		error: function (jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
		},
		failure: function (jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
		},
	});
}

/* Enter to save notes - Disabled form submit */
$(".commandBox").keypress(function (event) {
	var keycode = event.keyCode ? event.keyCode : event.which;
	if (keycode == "13") {
		$(".sendCommand").trigger("click");
		event.preventDefault();
	}
});

//DATE FORMAT FOR CHECKLIST
Date.prototype.toInputFormat = function () {
	var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
	var dd = this.getDate().toString();
	var yyyy = this.getFullYear().toString();
	return (
		(mm[1] ? mm : "0" + mm[0]) + "/" + (dd[1] ? dd : "0" + dd[0]) + "/" + yyyy
	); // padding
};

//DATE CHANGE FOR CHECKLIST - GIVEN DAYS
$(document)
	.off("keydown change blur", ".DocumentDate")
	.on("keydown change blur", ".DocumentDate", function (e) {
		var rowtr = $(this).closest("tr");
		var startdate = $(this).val();
		$elements = rowtr.find("[data-expiration]");
		$.each($elements, function (index, ele) {
			/* iterate through array or object */
			dataexpiration = $(ele).attr("data-expiration");
			$(ele).val("");
			if (dataexpiration != "") {
				//ADD DATE
				var date = new Date(startdate),
					days = parseInt(dataexpiration, 10);

				if (!isNaN(date.getTime())) {
					date.setDate(date.getDate() + days);
					$(ele).val(date.toInputFormat());
				}
			}
		});
	});

function checklistdatepicker_init() {
	//datetimepicker init
	$(".checklistdatepicker").datetimepicker({
		icons: {
			time: "fa fa-clock-o",
			date: "fa fa-calendar",
			up: "fa fa-chevron-up",
			down: "fa fa-chevron-down",
			previous: "fa fa-chevron-left",
			next: "fa fa-chevron-right",
			today: "fa fa-screenshot",
			clear: "fa fa-trash",
			close: "fa fa-remove",
		},
		format: "MM/DD/YYYY",
	});
}

$(document)
	.off("click", ".raiseworkupqueuepopup")
	.on("click", ".raiseworkupqueuepopup", function (e) {

		var WorkflowModuleUID = $(this).attr("data-WorkflowModuleUID");
		var OrderUID = $(this).attr("data-orderuid");

		$.ajax({
			url: base_url + 'OrderComplete/WorkflowForceEnableHardstop',
			type: 'POST',
			dataType: 'json',
			data: {'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID},
		})
		.done(function(data) {
			console.log("success");

			if(data.validation_error == 1) {
				swal({
					title: "<i class='icon-close2 icondanger' style='font-size: 27px;'></i>",
					html: data['message'],
					confirmButtonClass: "btn btn-success",
					allowOutsideClick: false,
					width: '700px',
					buttonsStyling: false
				}).catch(swal.noop);
				button.html(button_text);
				button.attr("disabled", false);
			} else {
				$("#WorkupQueue").modal("show");
				$(".forceenable_workflow").attr(
					"data-WorkflowModuleUID",
					WorkflowModuleUID
				);
				$(".forceenable_workflow").attr(
					"data-orderuid",
					OrderUID
				);
			}
			
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});

	});

	$(document).off('click', '.forceenable_workflow').on('click', '.forceenable_workflow', function (e) 
	{
		var OrderUID = $(this).attr('data-orderuid');
		var ClosingDate = $('#ProcessorChosenClosingDate').val();

		if(ClosingDate == "") {
			$("#ProcessorChosenClosingDate").focus();
			$.notify({icon:"icon-bell-check",message:'Please enter the closing date!'},{type:"danger",delay:2000 });

			return false;
		}

		//modal stc data values
		var STC = $("input[type=radio][name=STC]:checked").val();
		var STCAmount = $("#STCAmount").val();

		if(STC == "") {
			$.notify({icon:"icon-bell-check",message:'Please select the one month payment or zero STC or amount!'},{type:"danger",delay:2000 });
			return false;
		}

		if(STC == "Amount" && STCAmount == ""	) {
			$("#STCAmount").focus();
			$.notify({icon:"icon-bell-check",message:'Please enter the amount!'},{type:"danger",delay:2000 });
			return false;
		}

		var button = $(this);
		var button_text = $(this).html();

		var redirecturl = "Workup/index/"+OrderUID;
		
		if(OrderUID == '' || redirecturl == "" || redirecturl == "undefined") {
			var redirecturl = window.location.href;
		}

		var WorkflowModuleUID = $(this).attr('data-workflowmoduleuid');
		$.ajax({
			type: "POST",
			url: base_url + 'OrderComplete/forceenable_workflow',
			data: {'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID,'ClosingDate':ClosingDate,'STC':STC,'STCAmount':STCAmount},
			dataType: 'json',
			beforeSend: function () {
				button.attr("disabled", true);
				button.html('<i class="fa fa-spin fa-spinner"></i>');
				
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/

					$('#WorkupQueue').modal('hide');
					if (typeof(ModuleController) != "undefined" && ModuleController !== null && ModuleController == "WorkUp_Orders") {
						window.location.reload();
					} else {
						orderslist.ajax.reload(null, false); 
					}					
					$.notify({icon:"icon-bell-check",message:data['message']},{type:"success",delay:2000 });
					button.html(button_text);
					button.attr("disabled", false);
					$('#ProcessorChosenClosingDate').val('');
					//(redirecturl != '') ? window.open(redirecturl , '_blank') : false;
					
				} else {
					$.notify({
						icon: "icon-bell-check",
						message: data['message']
					}, {
						type: "danger",
						delay: 1000
					});
					button.html(button_text);
					button.attr("disabled", false);
					$('#ProcessorChosenClosingDate').val('');
				}
			},
			error: function (jqXHR) {
				swal({
					title: "<i class='icon-close2 icondanger'></i>",
					html: "<p>Failed to change</p>",
					confirmButtonClass: "btn btn-success",
					allowOutsideClick: false,
					width: '300px',
					buttonsStyling: false
				}).catch(swal.noop);
				button.html(button_text);
				button.attr("disabled", false);
			}
		});
			
	});

//followup queue

$(document)
	.off("click", ".followupqueuepopup")
	.on("click", ".followupqueuepopup", function (e) {
		$("#FollowupQueue").modal("show");
		var modalqueueuid = $(this).attr("data-queueuid");
		$(".btnraiseFollowup").attr(
			"data-WorkflowModuleUID",
			$(this).attr("data-WorkflowModuleUID")
		);
		$(".btnraiseFollowup").attr("data-orderuid", $(this).attr("data-orderuid"));
		$("#FollowupRemainder").val($(this).attr("data-followupdurationdate"));
		$("#FollowupraiseReason option[data-queueuid]").hide();
		$('#FollowupraiseReason option[data-queueuid=""]').show();
		$(
			'#FollowupraiseReason option[data-queueuid="' + modalqueueuid + '"]'
		).show();
		callselect2byid("FollowupraiseReason");
	});

$(document)
	.off("click", ".clearfollowupqueuepopup")
	.on("click", ".clearfollowupqueuepopup", function (e) {
		$("#ClearFollowupQueue").modal("show");
		var modalqueueuid = $(this).attr("data-queueuid");
		$(".btnclearFollowup").attr("data-orderuid", $(this).attr("data-orderuid"));
		$(".btnclearFollowup").attr(
			"data-WorkflowModuleUID",
			$(this).attr("data-WorkflowModuleUID")
		);
		$(".btnclearFollowup").attr("data-queueuid", modalqueueuid);
		$("#FollowupclearReason option[data-queueuid]").hide();
		$('#FollowupclearReason option[data-queueuid=""]').show();
		$(
			'#FollowupclearReason option[data-queueuid="' + modalqueueuid + '"]'
		).show();
		callselect2byid("FollowupclearReason");
	});

$(document)
	.off("submit", "#frmRaiseFollowup")
	.on("submit", "#frmRaiseFollowup", function (e) {
		e.preventDefault();
		e.stopPropagation();
		var OrderUID = $(".btnraiseFollowup").attr("data-orderuid");
		var WorkflowModuleUID = $(".btnraiseFollowup").attr(
			"data-workflowmoduleuid"
		);
		var queueuid = $(".btnraiseFollowup").attr("data-queueuid");

		var button = $(".btnraiseFollowup");
		var button_text = button.html();

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Raising');

		var formdata = new FormData($(this)[0]);
		formdata.append("OrderUID", OrderUID);
		formdata.append("WorkflowModuleUID", WorkflowModuleUID);
		formdata.append("QueueUID", queueuid);

		if (typeof Const_Page !== "undefined") {
			formdata.append("Page", Const_Page);
		}
		fn_raise_Followup(formdata, button, button_text);
	});

$(document)
	.off("submit", "#frmclearFollowup")
	.on("submit", "#frmclearFollowup", function (e) {
		e.preventDefault();
		e.stopPropagation();
		var OrderUID = $(".btnclearFollowup").attr("data-orderuid");
		var WorkflowModuleUID = $(".btnclearFollowup").attr(
			"data-workflowmoduleuid"
		);
		var queueuid = $(".btnclearFollowup").attr("data-queueuid");

		var button = $(".btnclearFollowup");
		var button_text = button.html();

		$(button).prop("disabled", true);
		$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Clearing');

		var formdata = new FormData($(this)[0]);
		formdata.append("OrderUID", OrderUID);
		formdata.append("WorkflowModuleUID", WorkflowModuleUID);
		formdata.append("QueueUID", queueuid);

		if (typeof Const_Page !== "undefined") {
			formdata.append("Page", Const_Page);
		}
		fn_clear_Followup(formdata, button, button_text);
	});

var fn_raise_Followup = function (formdata, button, button_text) {
	var accessurl = base_url + "OrderComplete/RaiseFollowup";

	$.ajax({
		type: "POST",
		url: accessurl,
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			button.attr("disabled", true);
			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/
				$.notify(
					{ icon: "icon-bell-check", message: data["message"] },
					{
						type: "success",
						delay: 2000,
						onClose: redirecturl(window.location.href),
					}
				);
			} else {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
				button.html(button_text);
				button.attr("disabled", false);
				$.each(data, function (k, v) {
					$("#" + k)
						.addClass("is-invalid")
						.closest(".form-group")
						.removeClass("has-success")
						.addClass("has-danger");
					$("#" + k + ".select2picker")
						.next()
						.find("span.select2-selection")
						.addClass("errordisplay");
				});
			}
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
			button.html(button_text);
			button.attr("disabled", false);
		},
	});
};

var fn_clear_Followup = function (formdata, button, button_text, action = true) {
	if (action) {
		var accessurl = base_url + "OrderComplete/ClearFollowup";	
	} else {
		var accessurl = base_url + "OrderComplete/ClearStaticQueueFollowup";
	}

	$.ajax({
		type: "POST",
		url: accessurl,
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			button.attr("disabled", true);
			button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/
				$.notify(
					{ icon: "icon-bell-check", message: data["message"] },
					{
						type: "success",
						delay: 2000,
						onClose: redirecturl(window.location.href),
					}
				);
			} else if (data.validation_error == 2) {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 4000,
					}
				);
			} else {
				$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "danger",
						delay: 1000,
					}
				);
				button.html(button_text);
				$(button).prop("disabled", false);
			}
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
			button.html(button_text);
			button.attr("disabled", false);
		},
	});
};

//checklist autosave
$(".tablist").click(function () {
	var formData = new FormData($("#frmordersummary")[0]);
	AutoUpdate(formData);
});

$(document).on("blur", ".checklists", function (event) {
	var td = $(this).closest("tr").find("td");
	var OrderUID = $('#OrderUID').val();
	var savechecklist = new FormData();
	savechecklist.append("OrderUID", OrderUID);
	td.each(function (index, el) {
		elementname = $(el).find(".checklists[name]").attr("name");
		elementval = $(el).find(".checklists[name]").val();
		if (elementname != "undefined") {
			savechecklist.append(elementname, elementval);
		}
	});

	savechecklist.append('autosave', 1);

	$.ajax({
		type: "POST",
		url: base_url + "Ordersummary/update_checklist",
		data: savechecklist,
		processData: false,
		contentType: false,
		success: function (data) {
			console.log("Updated");
		},
		error: function (jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
		},
		failure: function (jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
		},
	});
});

$(document).ready(function () {
	//init datepicker
	checklistdatepicker_init();
});

//select2 close modal
$(document).on("hide.bs.modal", function () {
	$("#select2-drop").css("display", "none");
});

//sidebar hover
$(document).off('mouseenter', '.sidebar-mini .sidebar').on('mouseenter', '.sidebar-mini .sidebar', function (e) {
	$(".side_new_icon").hide();
	$(".side_new_p").show();
	$(".side_new_i").show();
});
$(document).off('mouseleave', '.sidebar-mini .sidebar').on('mouseleave', '.sidebar-mini .sidebar', function (e) {
	$(".side_new_icon").show();
	$(".side_new_p").hide();
	$(".side_new_i").hide();
});
$(document).off('click', '.visible-on-sidebar-mini').on('click', '.visible-on-sidebar-mini', function (e) {
	$("body").removeClass("sidebar-mini");
	$(".side_new_icon").hide();
	$(".side_new_p").show();
	$(".side_new_i").show();
	$(".d-playbox").css("width","calc(100% - 260px)");
});
$(document).off('click', '.visible-on-sidebar-regular').on('click', '.visible-on-sidebar-regular', function (e) {
	$("body").addClass("sidebar-mini");
	$(".side_new_icon").show();
	$(".side_new_p").hide();
	$(".side_new_i").hide();
	$(".d-playbox").css("width","calc(100% - 80px)");
});
$(function() {
	$('.js-conveyor-1').jConveyorTicker({
	  anim_duration: 200
	});
  });

// Esclation
$(document).on('click','.EsclationOrderModal', function(){
	
	$('#EsclationOrderModal').find('#OrderUID').val($(this).data('orderuid'));
	$('#EsclationOrderModal').find('#HighlightUID').val($(this).data('highlightuid'));

	if($(this).hasClass('RaiseEsclationOrderModal')){

		$('#EsclationOrderModal').find('.modal-title').html('Raise Escalation');
		$('#EsclationOrderModal').find('.btnEsclationOrder').html('Raise Escalation');
		$('#EsclationOrderModal').find('#EsclationType').val('RaiseEscalation');
	}else{

		$('#EsclationOrderModal').find('.modal-title').html('Clear Escalation');
		$('#EsclationOrderModal').find('.btnEsclationOrder').html('Clear Escalation');
		$('#EsclationOrderModal').find('#EsclationType').val('ClearEscalation');
	}

	$('#EsclationOrderModal').modal('show');
});
// Esclation End

$(document).off("submit", "#formRaiseOrderEsclation").on("submit", "#formRaiseOrderEsclation", function (e) {
	e.preventDefault();
	var button = $('#EsclationOrderModal').find(".btnEsclationOrder");
	var button_text = $('#EsclationOrderModal').find(".btnEsclationOrder").html();

	var formdata = new FormData($(this)[0]);

	$.ajax({
		type: "POST",
		url: base_url + 'Esclation/RaiseEsclation',
		data: formdata,
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			button.attr("disabled", true);
			button.html('<i class="fa fa-spin fa-spinner"></i> Wait ...');
		},
		success: function (data) {

			if (data.Status == 0) {
				/*Sweet Alert MSG*/
				$.notify(
				{
					icon: "icon-bell-check",
					message: data.message,
				},
				{
					type: "success",
					delay: 1000,
				}
				);
				$("#EsclationOrderModal").modal("hide");
				setTimeout(function () {
					triggerpage(window.location.href);
				}, 1000);
			} else {
				$.notify(
				{
					icon: "icon-bell-check",
					message: data.message,
				},
				{
					type: "danger",
					delay: 1000,
				}
				);
				button.html(button_text);
				button.attr("disabled", false);
			}
		},
		error: function (jqXHR) {
			swal({
				title: "<i class='icon-close2 icondanger'></i>",
				html: "<p>Failed to Complete</p>",
				confirmButtonClass: "btn btn-success",
				allowOutsideClick: false,
				width: "300px",
				buttonsStyling: false,
			}).catch(swal.noop);
			button.html(button_text);
			button.attr("disabled", false);
		},
	});
});

/**
*Function Complete Re-Work Workflow 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Friday 28 August 2020.
*/
$(document)
	.off("click", "#btnReWorkComplete")
	.on("click", "#btnReWorkComplete", function (e) {
		e.preventDefault();
		var button = $(this);
		var button_text = $(this).html();
		var OrderUID = $("#OrderUID").val();
		var WorkflowModuleUID = $(this).attr("data-WorkflowModuleUID");
		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/ReWork_Complete",
			data: {
				OrderUID: OrderUID,
				WorkflowModuleUID: WorkflowModuleUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
						{
							icon: "icon-bell-check",
							message: data["message"],
						},
						{
							type: "success",
							delay: 1000,
						}
					);

					if (data.popup_message && data.popup_message != "") {
						swal({
							text: "REDIRECT TO " + data.popup_message + "?",
							type: "warning",
							showCancelButton: true,
							confirmButtonText: "Yes, Redirect!",
							cancelButtonText: "No, Stay here",
							confirmButtonClass: "btn btn-success",
							cancelButtonClass: "btn btn-info",
							buttonsStyling: false,
						}).then(
							function () {
								triggerpage(base_url + data.redirect);
							},
							function (dismiss) {
								triggerpage(window.location.href);
							}
						);
					} else {
						triggerpage(window.location.href);
					}
				} else {
					swal({
						text: "<h5>" + data.message + "</h5>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
						//timer: 5000,
						buttonsStyling: false,
					}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		})
	});

		
/* show alert message 
* @author Vishnupriya <vishnupriya.a@avanzegroup.com>
* @since Date : 28-08-2020
*/
$(document).off("click", ".alertMessage").on("click", ".alertMessage", function (e) {
	e.preventDefault();
	if ($('#adv_CustomerUID').val() != '30') {
		var checklist = $(this).attr("data-delete");
		var splitChecklist = checklist.split("~");
		if (splitChecklist[0]) {
			$.ajax({
				type: "post",
				url: base_url + "PreScreen/checklistAlert",
				data: {
					DocumentTypeUID: splitChecklist[0],
					OrderUID: OrderUID,
				},
				success: function (response) {
					if (response) {
						var result = JSON.parse(response);
						if (result.Status == 1) {
							$.each(result.message, function (index, obj) {
								$.notify({icon: "icon-bell-check", message: '<span class="pre-wrap">'+obj+'</span>', }, {type: "success", delay: 5000});
							});
						}
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
				},
				failure: function (jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
				},
			});
		}
	}
});

/* show alert message 
* @author Vishnupriya <vishnupriya.a@avanzegroup.com>
* @since Date : 28-08-2020
*/

$(document).on("focusin", ".questionlist", function (event) {
	event.preventDefault();
	if ($('#adv_CustomerUID').val() == '30') {

		var checklist = $(this).attr("data-delete");
		var splitChecklist = checklist.split("~");
		if (splitChecklist[0]) {
			$.ajax({
				type: "post",
				url: base_url + "PreScreen/checklistAlert",
				data: {
					DocumentTypeUID: splitChecklist[0],
					OrderUID: OrderUID,
				},
				success: function (response) {
					if (response) {
						var result = JSON.parse(response);
						if (result.Status == 1) {
							$.each(result.message, function (index, obj) {
								$.notify(
									{
										icon: "icon-bell-check",
										message: '<span class="pre-wrap">' + obj + '</span>',
									},
									{
										type: "success",
										delay: 5000,
									}
								);
							});
						}
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
				},
				failure: function (jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
				},
			});
		}
	}
});

/**
*Function update notes read
*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
*@since Thursday 10 September 2020.
*/

function update_notesread(OrderUID,WorkflowModuleUID)
{
	$.ajax({
		type:"POST",
		url : base_url + 'Priority_Report/update_notesread',
		data:{'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID},
		dataType :"json",
		success :function(response){
			//remove unread counts
			$('.viewnotes[data-orderuid="'+OrderUID+'"][data-workflowmoduleuid="'+WorkflowModuleUID+'"]').find('.badgenotification-unreadnotes').remove();
			$('.CommandsappendTable .badge-unread').remove();
		},
		error: function (jqXHR) {
			
		}
	})
}


/**
*Function fetch notes count by logged user
*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
*@since Thursday 10 September 2020.
*/
function fetch_notescounts() {
	//pass params

	var OrderArray = new Array();  
	var WorkflowArray = new Array();  

	if($('.viewnotes:visible').length) {

	$('.fetch-notescountstable tr:visible').each(function(orderindex, orderelement) {
		OrderArray[orderindex] = $(orderelement).find('.viewnotes').attr('data-orderuid'); // init the array.
		WorkflowArray[orderindex] = []; // init the array.
		$(orderelement).find('.viewnotes').each(function(workflowindex, ele) {
			WorkflowArray[orderindex].push($(ele).attr('data-workflowmoduleuid'));
		})
	});

	if (OrderArray.length === 0 || WorkflowArray.length === 0) {
		return false;
	}

	return new Promise(function (resolve, reject) {  
		SendAsyncAjaxRequest('POST', 'Priority_Report/fetch_tnotescountbyuser', {'OrderArray':OrderArray,'WorkflowArray':WorkflowArray}, 'JSON', true, true, function () {

		}).then(function (response) {

			if (response.success == 1) {
				$.each(response.returncount, function(orderkey, workflowarray) {
					/* iterate through array or object */
					//badgenotification-unreadnotes
					$.each(workflowarray, function(workflowkey, workflowvalue) {
						var ele = $('.viewnotes[data-orderuid="'+orderkey+'"][data-workflowmoduleuid="'+workflowkey+'"]');
						ele.find('.badgenotification-unreadnotes').remove();
						if(ele.html() !=  '' && workflowvalue > 0) {
							ele.html(ele.html()+'<span title="'+workflowvalue+' Unread Message(s)" class="badgenotification-unreadnotes">'+workflowvalue+'</span>');
						}
					})
				});

				$($.fn.dataTable.tables( true ) ).css('width', '100%');
				$($.fn.dataTable.tables(true)).DataTable().columns.adjust().fixedColumns().relayout();
			}

			resolve('success');

		}).catch(function (error) {

			console.log(error);
		});
	})
}
}

/**
*Function workflow tnotes comments
*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
*@since Thursday 10 September 2020.
*/
$(document).off('click', '.viewnotes').on('click', '.viewnotes', function (e) {

	var OrderUID = $(this).attr('data-orderuid');
	var WorkflowModuleUID = $(this).attr('data-workflowmoduleuid');
	$.ajax({
		type:"POST",
		url : base_url + 'Priority_Report/fetch_tnotes',
		data:{'OrderUID':OrderUID,'WorkflowModuleUID':WorkflowModuleUID},
		dataType :"json",
		cache: false,
		beforeSend: function () {
		},
		success :function(response){
			if (response.success == 1) {

				$('#WorkflowNotesmodal').modal('show');
				$('#WorkflowNotesmodal .modal-title').html(response.title);
				$('#WorkflowNotesmodal .modal-body').html(response.data);

				setTimeout(function () {

					update_notesread(OrderUID,WorkflowModuleUID);

				}, 5000);

			} else {
				$.notify({icon:"icon-bell-check",message:response.message},{type:"success",delay:2000 });

			}

		},
		error: function (jqXHR) {
		}
	})
});

//viewport function 
$.fn.isInViewport = function() {
	var elementTop = $(this).offset().top;
	var elementBottom = elementTop + $(this).outerHeight();
	var viewportTop = $(window).scrollTop();
	var viewportBottom = viewportTop + $(window).height();
	return elementBottom > viewportTop && elementTop < viewportBottom;
};

//update workflow level commands read
var fetchnotesinterval;
function markasreadcomments()
{
	var OrderUID = $('#OrderUID').val();
	var WorkflowModuleUID = $('#tNotes-WorkflowModuleUID').val();
	if(OrderUID && WorkflowModuleUID) {
		if ($('.CommandsappendTable').isInViewport()) {
			clearTimeout(fetchnotesinterval);
			fetchnotesinterval = setTimeout(function(){ update_notesread(OrderUID,WorkflowModuleUID) }, 3000); // Call AJAX every 10 seconds
		}
	}
}

// HOI Enable Rework
$(document).off('submit', '#frmEnableHOIRework').on('submit', '#frmEnableHOIRework', function (e) {

	e.preventDefault();
	e.stopPropagation();

	var button = $('.btnFrmEnableHOIReworkQueue');
	var button_text = button.html();

	$(button).prop('disabled', true);
	$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Enabling');

	var formdata = new FormData($(this)[0]);

    // Update Ordersummary
    var formData_ordersummary = new FormData($("#frmordersummary")[0]);
    AutoUpdate(formData_ordersummary);

    $.ajax({
    	type: "POST",
    	url: base_url + 'OrderComplete/EnableHOIReworkQueue',
    	data: formdata,
    	dataType: 'json',
    	cache: false,
    	processData: false,
    	contentType: false,
    	beforeSend: function () {
    		button.attr("disabled", true);
    		button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
    	},

    })
    .done(function(response) {
    	console.log("success", response);

    	if (response.validation_error == 0) {
    		/*Sweet Alert MSG*/

    		$.notify({icon:"icon-bell-check",message:response['message']},{type:"success",delay:2000,onClose:redirecturl(window.location.href) });
    		$('#HOIRework-Modal').modal('hide');
    	} else {
    		$.notify({
    			icon: "icon-bell-check",
    			message: response['message']
    		}, {
    			type: "danger",
    			delay: 1000
    		});
    		button.html(button_text);
    		button.attr("disabled", false);
    	}

    })
    .fail(function(jqXHR) {
    	console.error("error", jqXHR);
    	swal({
    		title: "<i class='icon-close2 icondanger'></i>",
    		html: "<p>Failed to Complete</p>",
    		confirmButtonClass: "btn btn-success",
    		allowOutsideClick: false,
    		width: '300px',
    		buttonsStyling: false
    	}).catch(swal.noop);
    })
    .always(function() {
    	console.log("complete");
    	button.html(button_text);
    	button.attr("disabled", false);
    });

});

// HOI Enable Rework
$(document).off('submit', '#frmCompleteHOIRework').on('submit', '#frmCompleteHOIRework', function (e) {

	e.preventDefault();
	e.stopPropagation();

	var button = $('.btnFrmCompleteHOIReworkQueue');
	var button_text = button.html();

	$(button).prop('disabled', true);
	$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Completing');

	var formdata = new FormData($(this)[0]);

    // Update Ordersummary
    var formData_ordersummary = new FormData($("#frmordersummary")[0]);
    AutoUpdate(formData_ordersummary);

    $.ajax({
    	type: "POST",
    	url: base_url + 'OrderComplete/CompleteHOIReworkQueue',
    	data: formdata,
    	dataType: 'json',
    	cache: false,
    	processData: false,
    	contentType: false,
    	beforeSend: function () {
    		button.attr("disabled", true);
    		button.html('<i class=""fa fa-spin fa-spinner"></i> Loading ...');
    	},

    })
    .done(function(response) {
    	console.log("success", response);

    	if (response.validation_error == 0) {
    		/*Sweet Alert MSG*/

    		$.notify({icon:"icon-bell-check",message:response['message']},{type:"success",delay:2000,onClose:redirecturl(window.location.href) });
    		$('#HOIReworkComplete-Modal').modal('hide');
    	} else {
    		$.notify({
    			icon: "icon-bell-check",
    			message: response['message']
    		}, {
    			type: "danger",
    			delay: 1000
    		});
    		button.html(button_text);
    		button.attr("disabled", false);
    	}

    })
    .fail(function(jqXHR) {
    	console.error("error", jqXHR);
    	swal({
    		title: "<i class='icon-close2 icondanger'></i>",
    		html: "<p>Failed to Complete</p>",
    		confirmButtonClass: "btn btn-success",
    		allowOutsideClick: false,
    		width: '300px',
    		buttonsStyling: false
    	}).catch(swal.noop);
    })
    .always(function() {
    	console.log("complete");
    	button.html(button_text);
    	button.attr("disabled", false);
    });

});

$(document).on("change", ".checklistfindings", function (event) {
	var checklist = $(this).closest('tr').attr("data-delete");
	var splitChecklist = checklist.split("~");

	// If "Check if the Master policy is uploaded" selected AS yes , Hoi  Insurance section to be hided 

	if (splitChecklist[0] == 2374 && WorkflowModuleUID == 39) {
		if ($(this).val() == 'Yes') {
			$('.HOI-INSURANCESUBJECTPROPERTYFOLDER').hide();	
		} else {
			$('.HOI-INSURANCESUBJECTPROPERTYFOLDER').show();
		}
		
	} else if (splitChecklist[0] == 2366 && WorkflowModuleUID == 39) {
		if ($(this).val() == 'Problem Identified') {
			// If this is selected as YES  then Flood Insurance section should  be Showed , IF  select as issue for the flood insurance section will be changed to ISsue
			$('.FLOODINSURANCEFLOODINSURANCEFOLDER').show();
			var selectedvar = $('.FLOODINSURANCEFLOODINSURANCEFOLDER');
			selectedvar.each(function(index, el) {
				$(el).find(".checklistfindings").val('Problem Identified');
				$(el).find(".checklistfindings").trigger('change');
				$(el).find(".checklistfindings").trigger('blur');
			});
		} else if($(this).val() == 'Yes') {
			$('.FLOODINSURANCEFLOODINSURANCEFOLDER').show();
			var selectedvar = $('.FLOODINSURANCEFLOODINSURANCEFOLDER');
			selectedvar.each(function(index, el) {
				$(el).find(".checklistfindings").val('empty');
				$(el).find(".checklistfindings").trigger('change');
				$(el).find(".checklistfindings").trigger('blur');
			});
		} else {
			$('.FLOODINSURANCEFLOODINSURANCEFOLDER').hide();
			var selectedvar = $('.FLOODINSURANCEFLOODINSURANCEFOLDER');
			selectedvar.each(function(index, el) {
				$(el).find(".checklistfindings").val('empty');
				$(el).find(".checklistfindings").trigger('change');
				$(el).find(".checklistfindings").trigger('blur');
			});
		}
		
	} else if (splitChecklist[0] == 2426 && WorkflowModuleUID == 39) {
		if ($(this).val() == 'Yes') {
			$('.FLOODINSURANCEFLOODINSURANCEFOLDER').show();	
		} else {
			$('.FLOODINSURANCEFLOODINSURANCEFOLDER').hide();
		}
		
	}
});

/**
*Function Initiate Pending 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Friday 09 October 2020
*/
$(document)
.off("click", "#btnInitiateSubQueuePending")
.on("click", "#btnInitiateSubQueuePending", function (e) {
	e.preventDefault();
	var button = $(this);
	var button_text = $(this).html();
	var OrderUID = $("#OrderUID").val();
	var WorkflowModuleUID = $(this).attr("data-WorkflowModuleUID");
	$.ajax({
		type: "POST",
		url: base_url + "OrderComplete/InitiateSubQueuePending",
		data: {
			OrderUID: OrderUID,
			WorkflowModuleUID: WorkflowModuleUID,
		},
		dataType: "json",
		cache: false,
		beforeSend: function () {
			addcardspinner("#Orderentrycard");
			$(button).html('<i class="fa fa-spin fa-spinner"></i> Initiating...');
			$(button).prop("disabled", true);
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/
				$.notify(
				{
					icon: "icon-bell-check",
					message: data["message"],
				},
				{
					type: "success",
					delay: 1000,
				}
				);

				if (data.popup_message && data.popup_message != "") {
					swal({
						text: "REDIRECT TO " + data.popup_message + "?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
					function () {
						triggerpage(base_url + data.redirect);
					},
					function (dismiss) {
						triggerpage(window.location.href);
					}
					);
				} else {
					triggerpage(window.location.href);
				}
			} else {
				swal({
					text: "<h5>" + data.message + "</h5>",
					type: "warning",
					confirmButtonText: "Ok",
					confirmButtonClass: "btn btn-success",
						//timer: 5000,
						buttonsStyling: false,
					}).catch(swal.noop);
			}
			$(button).prop("disabled", false);
			$(button).html(button_text);
		},
	}).always(function () {
		$(button).html(button_text);
		$(button).prop("disabled", false);
	})
});

/**
*Function Complete Pending 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Friday 09 October 2020
*/
$(document)
.off("click", "#btnCompleteSubPending")
.on("click", "#btnCompleteSubPending", function (e) {
	e.preventDefault();
	var button = $(this);
	var button_text = $(this).html();
	var OrderUID = $("#OrderUID").val();
	var WorkflowModuleUID = $(this).attr("data-WorkflowModuleUID");
	$.ajax({
		type: "POST",
		url: base_url + "OrderComplete/CompleteSubQueuePending",
		data: {
			OrderUID: OrderUID,
			WorkflowModuleUID: WorkflowModuleUID,
		},
		dataType: "json",
		cache: false,
		beforeSend: function () {
			addcardspinner("#Orderentrycard");
			$(button).html('<i class="fa fa-spin fa-spinner"></i> Initiating...');
			$(button).prop("disabled", true);
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/
				$.notify(
				{
					icon: "icon-bell-check",
					message: data["message"],
				},
				{
					type: "success",
					delay: 1000,
				}
				);

				if (data.popup_message && data.popup_message != "") {
					swal({
						text: "REDIRECT TO " + data.popup_message + "?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
					function () {
						triggerpage(base_url + data.redirect);
					},
					function (dismiss) {
						triggerpage(window.location.href);
					}
					);
				} else {
					triggerpage(window.location.href);
				}
			} else {
				swal({
					text: "<h5>" + data.message + "</h5>",
					type: "warning",
					confirmButtonText: "Ok",
					confirmButtonClass: "btn btn-success",
						//timer: 5000,
						buttonsStyling: false,
					}).catch(swal.noop);
			}
			$(button).prop("disabled", false);
			$(button).html(button_text);
		},
	}).always(function () {
		$(button).html(button_text);
		$(button).prop("disabled", false);
	})
});

/**
*Function Expiry Complete 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Friday 09 October 2020
*/
$(document)
.off("click", "#btnExpiryComplete")
.on("click", "#btnExpiryComplete", function (e) {
	e.preventDefault();
	var button = $(this);
	var button_text = $(this).html();
	var OrderUID = $("#OrderUID").val();
	var WorkflowModuleUID = $(this).attr("data-WorkflowModuleUID");
	$.ajax({
		type: "POST",
		url: base_url + "OrderComplete/ChecklistExpiryComplete",
		data: {
			OrderUID: OrderUID,
			WorkflowModuleUID: WorkflowModuleUID,
		},
		dataType: "json",
		cache: false,
		beforeSend: function () {
			addcardspinner("#Orderentrycard");
			$(button).html('<i class="fa fa-spin fa-spinner"></i> Completing...');
			$(button).prop("disabled", true);
		},
		success: function (data) {
			if (data.validation_error == 0) {
				/*Sweet Alert MSG*/
				$.notify(
				{
					icon: "icon-bell-check",
					message: data["message"],
				},
				{
					type: "success",
					delay: 1000,
				}
				);

				if (data.popup_message && data.popup_message != "") {
					swal({
						text: "REDIRECT TO " + data.popup_message + "?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
					function () {
						triggerpage(base_url + data.redirect);
					},
					function (dismiss) {
						triggerpage(window.location.href);
					}
					);
				} else {
					triggerpage(window.location.href);
				}
			} else {
				swal({
					text: "<h5>" + data.message + "</h5>",
					type: "warning",
					confirmButtonText: "Ok",
					confirmButtonClass: "btn btn-success",
						//timer: 5000,
						buttonsStyling: false,
					}).catch(swal.noop);
			}
			$(button).prop("disabled", false);
			$(button).html(button_text);
		},
	}).always(function () {
		$(button).html(button_text);
		$(button).prop("disabled", false);
	})
});

/**
*Function Clear static queue followup 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Thursday 05 November 2020.
*/
$(document)
.off("click", ".clearfollowupstaticqueuepopup")
.on("click", ".clearfollowupstaticqueuepopup", function (e) {
	$("#ClearFollowupStaticQueue").modal("show");
	var modalstaticqueueuid = $(this).attr("data-staticqueueuid");
	$(".btnclearStaticQueueFollowup").attr("data-orderuid", $(this).attr("data-orderuid"));
	$(".btnclearStaticQueueFollowup").attr(
		"data-WorkflowModuleUID",
		$(this).attr("data-WorkflowModuleUID")
		);
	$(".btnclearStaticQueueFollowup").attr("data-staticqueueuid", modalstaticqueueuid);
	$("#StaticQueueFollowupclearReason option[data-staticqueueuid]").hide();
	$(
		'#StaticQueueFollowupclearReason option[data-staticqueueuid="' + modalstaticqueueuid + '"]'
		).show();
	callselect2byid("StaticQueueFollowupclearReason");
});

$(document)
.off("submit", "#frmclearStaticQueueFollowup")
.on("submit", "#frmclearStaticQueueFollowup", function (e) {
	e.preventDefault();
	e.stopPropagation();
	var OrderUID = $(".btnclearStaticQueueFollowup").attr("data-orderuid");
	var WorkflowModuleUID = $(".btnclearStaticQueueFollowup").attr(
		"data-workflowmoduleuid"
		);
	var staticqueueuid = $(".btnclearStaticQueueFollowup").attr("data-staticqueueuid");

	var button = $(".btnclearStaticQueueFollowup");
	var button_text = button.html();

	$(button).prop("disabled", true);
	$(button).html('<i class="fa fa-spin fa-spinner"></i> ...Clearing');

	var formdata = new FormData($(this)[0]);
	formdata.append("OrderUID", OrderUID);
	formdata.append("WorkflowModuleUID", WorkflowModuleUID);
	formdata.append("StaticQueueUID", staticqueueuid);

	if (typeof Const_Page !== "undefined") {
		formdata.append("Page", Const_Page);
	}
	fn_clear_Followup(formdata, button, button_text, false);
});

//Holiday Modal
$(document).off('click', '.BtnHolidaysList').on('click', '.BtnHolidaysList', function (e) {

	$.ajax({
		type:"POST",
		url : base_url + 'CommonController/fetch_HolidayListPopup',
		dataType :"json",
		cache: false,
		beforeSend: function () {
		},
		success :function(response){
			$('#appendholidaylistmodal').html(response.data);
			$('#modal-HolidayList').modal('show');

		},
		error: function (jqXHR) {
		}
	})
});

/**
*Function Check KickBack Is Enabled 
*@author SathishKumar <sathish.kumar@avanzegroup.com>
*@since Sunday 22 November 2020.
*/
$(document)
.off("click", "#btnMovetoKickBack")
.on("click", "#btnMovetoKickBack", function (e) {
	e.preventDefault();
		
	var button = $(this);
	var button_text = $(this).html();
	var OrderUID = $("#OrderUID").val();
	var WorkflowModuleUID = $(this).attr("data-workflowmoduleuid");

	swal({
		text: "Want to move the order to kick back queue?",
		type: "warning",
		showCancelButton: true,
		confirmButtonText: "Yes!",
		cancelButtonText: "No",
		confirmButtonClass: "btn btn-success",
		cancelButtonClass: "btn btn-info",
		buttonsStyling: false,
	}).then(
	function () {

		$.ajax({
			type: "POST",
			url: base_url + "OrderComplete/InitiateOrderMovetoKickBack",
			data: {
				OrderUID: OrderUID,
				WorkflowModuleUID: WorkflowModuleUID,
			},
			dataType: "json",
			cache: false,
			beforeSend: function () {
				addcardspinner("#Orderentrycard");
				$(button).html('<i class="fa fa-spin fa-spinner"></i> Initiating...');
				$(button).prop("disabled", true);
			},
			success: function (data) {
				if (data.validation_error == 0) {
					/*Sweet Alert MSG*/
					$.notify(
					{
						icon: "icon-bell-check",
						message: data["message"],
					},
					{
						type: "success",
						delay: 1000,
					}
					);

					if (data.popup_message && data.popup_message != "") {
					swal({
						text: "REDIRECT TO " + data.popup_message + "?",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Yes, Redirect!",
						cancelButtonText: "No, Stay here",
						confirmButtonClass: "btn btn-success",
						cancelButtonClass: "btn btn-info",
						buttonsStyling: false,
					}).then(
					function () {
						triggerpage(base_url + data.redirect);
					},
					function (dismiss) {
						triggerpage(window.location.href);
					}
					);
				} else {
					triggerpage(window.location.href);
				}

				} else {
					swal({
						text: "<h5>" + data.message + "</h5>",
						type: "warning",
						confirmButtonText: "Ok",
						confirmButtonClass: "btn btn-success",
							//timer: 5000,
							buttonsStyling: false,
						}).catch(swal.noop);
				}
				$(button).prop("disabled", false);
				$(button).html(button_text);
			},
		}).always(function () {
			$(button).html(button_text);
			$(button).prop("disabled", false);
		})

	},
	function (dismiss) {
		
	}
	);

});