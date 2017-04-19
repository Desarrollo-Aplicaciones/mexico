{if isset($error)}
 <p style="color:red">{l s='An error occured, please try again later.' mod='payulatam'}</p>
{else} 
     
     
<form  method="POST" action="./modules/payulatam/oxxo.php" id="formOxxo" name="formOxxo" autocomplete="off" >
  
    <div class="contend-form" >        
        <input type="hidden" value="payuOxxo" id="pagar" name="pagar" />
                       
    </div>       

</form>

{/if}

