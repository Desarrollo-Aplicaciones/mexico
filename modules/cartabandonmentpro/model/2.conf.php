<?php
$leftColumn  = false;
$rightColumn = false;
$txtsRight	 = 0;
$txtsLeft	 = 0;
$txtsCenter	 = 3;
$colors 	 = 2;
$cartProducts = '<tr><td>%NAME%</td></tr><tr><td>%IMG%</td></tr>';
$content = 
'
<div width="600" align="center" valign="top" style="background-color:#%color_1%;">
	<table width="600" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
			<td width="166" valign="middle">
				%SHOP_LINK_OPEN%<img style="text-decoration: none; display: block; color:#476688; font-size:30px;display:block;vertical-align:top;" 
				src="%logo%" border="0">
				%SHOP_LINK_CLOSE%
			</td>
		</tr>
		</tbody>
	</table>
	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
		<tr>
			<td width="600" height="20px"><hr style="margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee"></td>
		</tr>
	</tbody>
	</table>  

	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
		<tr><td bgcolor="#%color_2%">
			<table width="600" border="0" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
					<td width="280" align="left" valign="top">
							<table width="280" border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td width="280" align="left" valign="top" style="padding: 5px;">
											%center_1%
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td width="320" align="right" valign="top">
							<table width="320" border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td width="40" height="35"></td>
										<td width="280" height="35" align="left" style="padding: 5px;">
											%center_2%
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td></tr>
		</tbody>
	</table>

	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<td width="600" height="22">
					<hr style="margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee">
				</td>
			</tr>
			<tr>
				<td width="600" height="32" bgcolor="#%color_1%">
					<table width="600" border="0" cellpadding="0" cellspacing="0"> 
						<tbody>
							<tr>
								<td width="40" height="32" align="center">
									%center_3%
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
';

$contentEdit1 = '
<div id="color_2_1_1_edit" width="600" align="center" valign="top" style="background-color:#%color_1%;">
	<input id="color_picker_2_1_1_edit" class="color" name="color_picker_2_1_1" 
	onchange="updateColor(\'color_2_1_1_edit\',this.color.toString());" value="%color_1%" />
	<table width="600" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
			<td width="166" valign="middle">
				%SHOP_LINK_OPEN%<img style="text-decoration: none; display: block; color:#476688; font-size:30px;display:block;vertical-align:top;" 
				src="%logo%" border="0">
				%SHOP_LINK_CLOSE%
			</td>
		</tr>
		</tbody>
	</table>
	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
		<tr>
			<td width="600" height="20px"><hr style="margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee"></td>
		</tr>
	</tbody>
	</table>  

	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
		<tr><td id="color_2_2_1_edit" bgcolor="#%color_2%">
			<input id="color_picker_2_2_1_edit" class="color" name="color_picker_2_2_1" onchange="updateColor(\'color_2_2_1_edit\',this.color.toString())" value="%color_2%" />
			<table width="600" border="0" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
					<td width="280" align="left" valign="top">
							<table width="280" border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td width="280" align="left" valign="top" style="padding: 5px;">
											<textarea name="center_2_1_1" id="tpl2_center_1_1">%center_1%</textarea>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td width="320" align="right" valign="top">
							<table width="320" border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td width="40" height="35"></td>
										<td width="280" height="35" align="left" style="padding: 5px;">
											<textarea name="center_2_2_1" id="tpl2_center_2_1">%center_2%</textarea>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td></tr>
		</tbody>
	</table>

	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<td width="600" height="22">
					<hr style="margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee">
				</td>
			</tr>
			<tr>
				<td width="600" height="32" bgcolor="#%color_1%">
					<table width="600" border="0" cellpadding="0" cellspacing="0"> 
						<tbody>
							<tr>
								<td width="40" height="32" align="center">
									<textarea name="center_2_3_1" id="tpl2_center_3_1_edit">%center_3%</textarea>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
';

$contentEdit2 = '
<div id="color_2_1_2_edit" width="600" align="center" valign="top" style="background-color:#%color_1%;">
	<input id="color_picker_2_1_2_edit" class="color" name="color_picker_2_1_2" 
	onchange="updateColor(\'color_2_1_2_edit\',this.color.toString());" value="%color_1%" />
	<table width="600" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
			<td width="166" valign="middle">
				%SHOP_LINK_OPEN%<img style="text-decoration: none; display: block; color:#476688; font-size:30px;display:block;vertical-align:top;" 
				src="%logo%" border="0">
				%SHOP_LINK_CLOSE%
			</td>
		</tr>
		</tbody>
	</table>
	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
		<tr>
			<td width="600" height="20px"><hr style="margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee"></td>
		</tr>
	</tbody>
	</table>  

	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
		<tr><td id="color_2_2_2_edit" bgcolor="#%color_2%">
			<input id="color_picker_2_2_2_edit" class="color" name="color_picker_2_2_2" onchange="updateColor(\'color_2_2_2_edit\',this.color.toString())" value="%color_2%" />
			<table width="600" border="0" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
					<td width="280" align="left" valign="top">
							<table width="280" border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td width="280" align="left" valign="top" style="padding: 5px;">
											<textarea name="center_2_1_2" id="tpl2_center_1_2">%center_1%</textarea>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td width="320" align="right" valign="top">
							<table width="320" border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td width="40" height="35"></td>
										<td width="280" height="35" align="left" style="padding: 5px;">
											<textarea name="center_2_2_2" id="tpl2_center_2_2">%center_2%</textarea>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td></tr>
		</tbody>
	</table>

	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<td width="600" height="22">
					<hr style="margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee">
				</td>
			</tr>
			<tr>
				<td width="600" height="32" bgcolor="#%color_1%">
					<table width="600" border="0" cellpadding="0" cellspacing="0"> 
						<tbody>
							<tr>
								<td width="40" height="32" align="center">
									<textarea name="center_2_3_2" id="tpl2_center_3_2_edit">%center_3%</textarea>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
';

$contentEdit3 = '
<div id="color_2_1_3_edit" width="600" align="center" valign="top" style="background-color:#%color_1%;">
	<input id="color_picker_2_1_3_edit" class="color" name="color_picker_2_1_3" 
	onchange="updateColor(\'color_2_1_3_edit\',this.color.toString());" value="%color_1%" />
	<table width="600" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
			<td width="166" valign="middle">
				%SHOP_LINK_OPEN%<img style="text-decoration: none; display: block; color:#476688; font-size:30px;display:block;vertical-align:top;" 
				src="%logo%" border="0">
				%SHOP_LINK_CLOSE%
			</td>
		</tr>
		</tbody>
	</table>
	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
		<tr>
			<td width="600" height="20px"><hr style="margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee"></td>
		</tr>
	</tbody>
	</table>  

	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
		<tr><td id="color_2_2_3_edit" bgcolor="#%color_2%">
			<input id="color_picker_2_2_3_edit" class="color" name="color_picker_2_2_3" onchange="updateColor(\'color_2_2_3_edit\',this.color.toString())" value="%color_2%" />
			<table width="600" border="0" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
					<td width="280" align="left" valign="top">
							<table width="280" border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td width="280" align="left" valign="top" style="padding: 5px;">
											<textarea name="center_2_1_3" id="tpl2_center_1_3">%center_1%</textarea>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td width="320" align="right" valign="top">
							<table width="320" border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td width="40" height="35"></td>
										<td width="280" height="35" align="left" style="padding: 5px;">
											<textarea name="center_2_2_3" id="tpl2_center_2_3">%center_2%</textarea>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td></tr>
		</tbody>
	</table>

	<table width="600" border="0" cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<td width="600" height="22">
					<hr style="margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee">
				</td>
			</tr>
			<tr>
				<td width="600" height="32">
					<table width="600" border="0" cellpadding="0" cellspacing="0"> 
						<tbody>
							<tr>
								<td width="40" height="32" align="center">
									<textarea name="center_2_3_3" id="tpl2_center_3_3_edit">%center_3%</textarea>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
';