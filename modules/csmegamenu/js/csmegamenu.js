$(window).load(function(){
		$("#opt_product_autocomplete_input")
			.autocomplete("ajax_products_list.php", {
				minChars: 1,
				autoFill: true,
				max:20,
				matchContains: true,
				mustMatch:true,
				scroll:false,
				cacheLength:0,
				formatItem: function(item) {
					return item[1] + " - " + item[0];
				}
		}).result(this.addProductIntoMenu);
		$('#opt_result_product_autocomplete').delegate('.delProducts', 'click', function(){
			self.delProducts($(this).attr('name'));
		});
});
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
function displayFormOption(typeOption)
{
	$("select[name='type_option'] option").removeAttr('selected');
	$("select[name='type_option'] option[value='" + typeOption + "']").attr('selected','selected');
	//
	var eActive = "option_type_" + typeOption;
	$(".div_content_option").children().each(function(){
		if($(this).attr("class") == eActive)
			$(this).show();
		else
			$(this).hide();
	});
}


this.addProductIntoMenu = function(event, data, formatted)
{
	if (data == null)
		return false;
	var productId = data[1];
	var productName = data[0];
	var $divResultAutocomplete = $('#opt_result_product_autocomplete');
	var $inputResultAutocompleteId = $('#input_hidden_id');
	var $inputResultAutocompleteName = $('#input_hidden_name');
	$divResultAutocomplete.html($divResultAutocomplete.html() + productName + ' <span class="delProducts" name="' + productId + '" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />');
		$inputResultAutocompleteName.val($inputResultAutocompleteName.val() + productName + '¤');
		$inputResultAutocompleteId.val($inputResultAutocompleteId.val() + productId + '-');
	$('#opt_product_autocomplete_input').val('');
	$('#opt_product_autocomplete_input').setOptions({
			extraParams: {
				excludeIds : self.getResultProductIds()
			}
		});
	return false;
}

this.getResultProductIds = function(data)
	{
		if (data == null)
		return false;
		var id_product = data[1];
		if ($('#input_hidden_id').val() === undefined)
			return '';
		var ids = id_product + ',';
		ids += $('#input_hidden_id').val().replace(/\\-/g,',').replace(/\\,$/,'');
		ids = ids.replace(/\,$/,'');

		return ids;
	}


this.delProducts = function(id)
	{
		var div = getE('opt_result_product_autocomplete');
		var input = getE('input_hidden_id');
		var name = getE('input_hidden_name');

		// Cut hidden fields in array
		var inputCut = input.value.split('-');
		var nameCut = name.value.split('¤');

		if (inputCut.length != nameCut.length)
			return jAlert('Bad size');

		// Reset all hidden fields
		input.value = '';
		name.value = '';
		div.innerHTML = '';
		for (i in inputCut)
		{
			// If empty, error, next
			if (!inputCut[i] || !nameCut[i])
				continue ;

			// Add to hidden fields no selected products OR add to select field selected product
			if (inputCut[i] != id)
			{
				input.value += inputCut[i] + '-';
				name.value += nameCut[i] + '¤';
				div.innerHTML += nameCut[i] + ' <span class="delProducts" name="' + inputCut[i] + '" style="cursor: pointer;"><img src="../img/admin/delete.gif" /></span><br />';
			}
			else
				$('#selectAccessories').append('<option selected="selected" value="' + inputCut[i] + '-' + nameCut[i] + '">' + inputCut[i] + ' - ' + nameCut[i] + '</option>');
		}
		$('#opt_product_autocomplete_input').setOptions({
			extraParams: {excludeIds : self.getResultProductIds()}
		});
	};
	
function checkDelBoxesMenu(pForm, boxName, parent)
{
	for (i = 0; i < pForm.elements.length; i++)
		if (pForm.elements[i].name == boxName)
			pForm.elements[i].checked = parent;
	
}
function checkCheckAllBoxes (pForm,boxName)
{
	for (i = 0; i < pForm.elements.length; i++)
		if (pForm.elements[i].name == boxName)
		{
			if (pForm.elements[i].checked == false)
				return false;
		}
	return true;
}
function checkCheckItemBox(pForm,boxParrentName,boxName,child) //checkCheckItemBox
{
	
	var boxParrent;
	for (i = 0; i < pForm.elements.length; i++)
	{
		if (pForm.elements[i].name == boxParrentName)
		{
			boxParrent = pForm.elements[i];
		}
	}
	if(child == false)
		boxParrent.checked = false;
	else 
		if( checkCheckAllBoxes(pForm,boxName)== true)
			boxParrent.checked = true;
}
function checkallCMSBoxes(checked)
{
	if (checked)
		$('.cmsBox').attr('checked', "checked");
	else
		$('.cmsBox').attr('checked', "");
}