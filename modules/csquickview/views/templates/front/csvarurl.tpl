{if !isset($resize_type)}{$resize_type=0}{/if}
{if !isset($resize_width)}{$resize_width=50}{/if}
{if !isset($resize_height)}{$resize_height=65}{/if}
{if !isset($itemsclass)}{$itemsclass=''}{$num_item=0}{/if}
<script type="text/javascript">
//<![CDATA[
$(document).ajaxComplete(function( event,request, settings ) {
	_csJnit(IS,n);
 });
function isMobile() {
		if( navigator.userAgent.match(/Android/i) ||
			navigator.userAgent.match(/webOS/i) ||
			navigator.userAgent.match(/iPad/i) ||
			navigator.userAgent.match(/iPhone/i) ||
			navigator.userAgent.match(/iPod/i)
			){
				return true;
		}
		return false;
	}
if(!isMobile())
{
	if (typeof CS == 'undefined') CS = {};
	CS.QuickView = {
		BASE_URL : '{$base_dir}',
		QS_FRM_TYPE : {$resize_type},
		QS_FRM_WIDTH : {$resize_width},
		QS_FRM_HEIGHT : {$resize_height}
	};
	if(CS.QuickView.QS_FRM_TYPE == 0 ){
		CS.QuickView.QS_FRM_WIDTH = CS.QuickView.QS_FRM_WIDTH + "%";
		CS.QuickView.QS_FRM_HEIGHT = CS.QuickView.QS_FRM_HEIGHT + "%";
	}
	var strItem='{$itemsclass}';
	
	var n={$num_item};
	var IS={};
	if(n>1)
	{
		var arrItem=strItem.split(',');
		
		for(var i=0;i<n;i++)
		{
			IS[i]={
				itemClass:$.trim(arrItem[i])
			}
		}
	}
	else if(n==1)
	{
		IS[0]={
				itemClass:$.trim(strItem)
			}		
	}
	else
	{
		n=0;
		IS[0]={
				itemClass:''
			}
	}
}
//]]
</script>