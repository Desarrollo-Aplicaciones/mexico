{if isset($error)}
 <p style="color:red">{l s='An error occured, please try again later.' mod='payulatam'}</p>
{else}
    

 
 
<div >
     
     
<form  method="POST" action="./modules/payulatam/payuEfecty.php" id="formEfecty" name="formEfecty" autocomplete="off" >

    <div class="contend-form" >        
        <input type="hidden" value="payuefecty" id="pagar" name="pagar" />
                            
    </div>

       

</form>
</div>






{/if}

