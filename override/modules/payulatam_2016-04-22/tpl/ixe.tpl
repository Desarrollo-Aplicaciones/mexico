{if isset($error)}
 <p style="color:red">{l s='An error occured, please try again later.' mod='payulatam'}</p>
{else} 
     
<form  method="POST" action="./modules/payulatam/ixe.php" id="formIxe" name="formIxe" autocomplete="off" >
  
    <div class="contend-form" >        
        <input type="hidden" value="payuIxe" id="pagar" name="pagar" />
                            
    </div>       

</form>

{/if}
