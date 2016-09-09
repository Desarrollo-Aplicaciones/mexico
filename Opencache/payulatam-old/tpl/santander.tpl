{if isset($error)}
 <p style="color:red">{l s='An error occured, please try again later.' mod='payulatam'}</p>
{else} 
     
<form  method="POST" action="./modules/payulatam/santander.php" id="formSantander" name="formSantander" autocomplete="off" >
  
    <div class="contend-form" >        
        <input type="hidden" value="payuSantander" id="pagar" name="pagar" />
                            
    </div>       

</form>

{/if}
