{if isset($error)}
 <p style="color:red">{l s='An error occured, please try again later.' mod='payulatam'}</p>
{else} 
 
   
     
<form  method="POST" action="./modules/payulatam/sevenEleven.php" id="formSEleven" name="formSEleven" autocomplete="off" >
  
    <div class="contend-form" >        
        <input type="hidden" value="formSEleven" id="pagar" name="pagar" />
                            
    </div>       

</form>


{/if}
