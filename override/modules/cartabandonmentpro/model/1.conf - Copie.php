<?php
$leftColumn  = false;
$rightColumn = false;
$txtsRight	 = 0;
$txtsLeft	 = 0;
$txtsCenter	 = 2;
$colors 	 = 4;
$cartProducts = '<tr><td>%IMG%</td><td>%NAME%<br>%DESC%</td></tr>';
$content = '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:rgb(42, 55, 78)" >
<tbody>
<tr>
<td align="center" bgcolor="#%color_1%">
<table  cellpadding="0" cellspacing="0" border="0">
	<tbody>                            
		<tr>
			<td class="w640"  width="640" height="10"></td>
		</tr>

		<tr>
			<td align="center" class="w640"  width="640" height="20"></td>
		</tr>
		<tr>
			<td class="w640"  width="640" height="10"></td>
		</tr>


		<!-- entete -->
		<tr class="pagetoplogo">
			<td bgcolor="#%color_2%">
				<table cellpadding="0" cellspacing="0" border="0" bgcolor="#%color_2%">
					<tbody>
						<tr>
							<td class="w30"  width="30"></td>
							<td  class="w580"  width="580" valign="middle" align="left">
								<div class="pagetoplogo-content">
									<center><img style="text-decoration: none; display: block; color:#476688; font-size:30px;" src="%logo%" alt="Mon Logo"/></center>
								</div>
							</td> 
							<td class="w30"  width="30"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>

		<!-- separateur horizontal -->
		<tr>
			<td  class="w640"  width="640" height="1" bgcolor="#d7d6d6"></td>
		</tr>

		 <!-- contenu -->
		<tr class="content">
			<td class="w640" class="w640"  width="640" bgcolor="#%color_3%">
				<table class="w640"  width="640" cellpadding="0" cellspacing="0" border="0">
					<tbody>
						<tr>
							<td  class="w30"  width="30"></td>
							<td  class="w580"  width="580">
								<!-- une zone de contenu -->
								<table class="w580"  width="580" cellpadding="0" cellspacing="0" border="0">
									<tbody>                                                            
										<tr>
											<td class="w580"  width="580">
												%center_1%
											</td>
										</tr>
									</tbody>
								</table>
								<!-- fin zone -->                                                   
							</td>
							<td class="w30" class="w30"  width="30"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>

		<!--  separateur horizontal de 15px de  haut-->
		<tr>
			<td class="w640"  width="640" height="1" bgcolor="#ffffff"></td>
		</tr>

		<!-- pied de page -->
		<tr class="pagebottom">
			<td class="w640"  width="640" bgcolor="#%color_4%">
				<table class="w640"  width="640" cellpadding="0" cellspacing="0" border="0" bgcolor="#%color_4%">
					<tbody>
						<tr>
							<td colspan="5" height="10"></td>
						</tr>
						<tr>
							<td class="w30"  width="30"></td>
							<td class="w580"  width="580" valign="top">
								%center_2%
							</td>

							<td class="w30"  width="30"></td>
						</tr>
						<tr>
							<td colspan="5" height="10"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td class="w640"  width="640" height="60"></td>
		</tr>
	</tbody>
</table>';

$contentEdit1 = '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:rgb(42, 55, 78)" >
	<tbody>
		<tr>
			<td id="color_1_1_1_edit" align="center" bgcolor="#%color_1%">
				<input id="color_picker_1_1_1_edit" class="color" name="color_picker_1_1_1" onchange="updateColorEdit(\'color_1_1_1_edit\', \'color_picker_1_1_1_edit\');" value="%color_1%" />
				<table  cellpadding="0" cellspacing="0" border="0">
					<tbody>
						<tr>
							<td class="w640"  width="640" height="10"></td>
						</tr>

						<tr>
							<td align="center" class="w640"  width="640" height="20"></td>
						</tr>
						<tr>
							<td class="w640"  width="640" height="10"></td>
						</tr>


						<!-- entete -->
						<tr class="pagetoplogo">
							<td id="color_1_2_1_edit" bgcolor="#%color_2%">
								<table cellpadding="0" cellspacing="0" border="0">
									<tbody>
										<tr><td colspan="3"><input id="color_picker_1_2_1_edit" class="color" name="color_picker_1_2_1" onchange="updateColorEdit(\'color_1_2_1_edit\', \'color_picker_1_2_1_edit\');" value="%color_2%" /></td></tr>
										<tr>
											<td class="w30"  width="30"></td>
											<td  class="w580"  width="580" valign="middle" align="left">
												<div class="pagetoplogo-content">
													<center><img  style="text-decoration: none; display: block; color:#476688; font-size:30px;" src="../img/logo.jpg" alt="Mon Logo"/></center>
												</div>
											</td> 
											<td class="w30"  width="30"></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<!-- separateur horizontal -->
						<tr>
							<td  class="w640"  width="640" height="1" bgcolor="#d7d6d6"></td>
						</tr>

						 <!-- contenu -->
						<tr class="content">
							<td id="color_1_3_1_edit" class="w640" class="w640"  width="640" bgcolor="#%color_3%">
								<input id="color_picker_1_3_1_edit" class="color" name="color_picker_1_3_1" onchange="updateColorEdit(\'color_1_3_1_edit\', \'color_picker_1_3_1_edit\');" value="%color_3%" />
								<table class="w640"  width="640" cellpadding="0" cellspacing="0" border="0">
									<tbody>
										<tr>
											<td  class="w30"  width="30"></td>
											<td  class="w580"  width="580">
												<!-- une zone de contenu -->
												<table class="w580"  width="580" cellpadding="0" cellspacing="0" border="0">
													<tbody>                                                            
														<tr>
															<td class="w580"  width="580">
																<textarea name="center_1_1_1" id="tpl1_center_1_1_edit">%center_1%</textarea>
															</td>
														</tr>
													</tbody>
												</table>
												<!-- fin zone -->                                                   
											</td>
											<td class="w30" class="w30"  width="30"></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<!--  separateur horizontal de 15px de  haut-->
						<tr>
							<td class="w640"  width="640" height="1" bgcolor="#ffffff"></td>
						</tr>

						<!-- pied de page -->
						<tr class="pagebottom">
							<td id="color_1_4_1_edit" class="w640"  width="640" bgcolor="#%color_4%">
								<table class="w640"  width="640" cellpadding="0" cellspacing="0" border="0" >
									<tbody>
										<tr><td colspan=3><input id="color_picker_1_4_1_edit" class="color" name="color_picker_1_4_1" onchange="updateColorEdit(\'color_1_4_1_edit\', \'color_picker_1_4_1_edit\');" value="%color_4%" /></td></tr>
										<tr>
											<td colspan="5" height="10"></td>
										</tr>
										<tr>
											<td class="w30"  width="30"></td>
											<td class="w580"  width="580" valign="top">
												<textarea name="center_1_2_1" id="tpl1_center_1_2_1_edit">%center_2%</textarea>
											</td>

											<td class="w30"  width="30"></td>
										</tr>
										<tr>
											<td colspan="5" height="10"></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td class="w640"  width="640" height="60"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>';

$contentEdit2 = '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:rgb(42, 55, 78)" >
	<tbody>
		<tr>
			<td id="color_1_1_2_edit" align="center" bgcolor="#%color_1%">
				<input id="color_picker_1_1_2_edit" class="color" name="color_picker_1_1_2" onchange="updateColorEdit(\'color_1_1_2_edit\', \'color_picker_1_1_2_edit\');" value="%color_1%" />
				<table  cellpadding="0" cellspacing="0" border="0">
					<tbody>
						<tr>
							<td class="w640"  width="640" height="10"></td>
						</tr>

						<tr>
							<td align="center" class="w640"  width="640" height="20"></td>
						</tr>
						<tr>
							<td class="w640"  width="640" height="10"></td>
						</tr>


						<!-- entete -->
						<tr class="pagetoplogo">
							<td id="color_1_2_2_edit" bgcolor="#%color_2%">
								<table cellpadding="0" cellspacing="0" border="0">
									<tbody>
										<tr><td colspan="3"><input id="color_picker_1_2_2_edit" class="color" name="color_picker_1_2_2" onchange="updateColorEdit(\'color_1_2_2_edit\', \'color_picker_1_2_2_edit\');" value="%color_2%" /></td></tr>
										<tr>
											<td class="w30"  width="30"></td>
											<td  class="w580"  width="580" valign="middle" align="left">
												<div class="pagetoplogo-content">
													<center><img  style="text-decoration: none; display: block; color:#476688; font-size:30px;" src="../img/logo.jpg" alt="Mon Logo"/></center>
												</div>
											</td> 
											<td class="w30"  width="30"></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<!-- separateur horizontal -->
						<tr>
							<td  class="w640"  width="640" height="1" bgcolor="#d7d6d6"></td>
						</tr>

						 <!-- contenu -->
						<tr class="content">
							<td id="color_1_3_2_edit" class="w640" class="w640"  width="640" bgcolor="#%color_3%">
								<input id="color_picker_1_3_2_edit" class="color" name="color_picker_1_3_2" onchange="updateColorEdit(\'color_1_3_2_edit\', \'color_picker_1_3_2_edit\');" value="%color_3%" />
								<table class="w640"  width="640" cellpadding="0" cellspacing="0" border="0">
									<tbody>
										<tr>
											<td  class="w30"  width="30"></td>
											<td  class="w580"  width="580">
												<!-- une zone de contenu -->
												<table class="w580"  width="580" cellpadding="0" cellspacing="0" border="0">
													<tbody>                                                            
														<tr>
															<td class="w580"  width="580">
																<textarea name="center_1_1_2" id="tpl1_center_1_2_edit">%center_1%</textarea>
															</td>
														</tr>
													</tbody>
												</table>
												<!-- fin zone -->                                                   
											</td>
											<td class="w30" class="w30"  width="30"></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<!--  separateur horizontal de 15px de  haut-->
						<tr>
							<td class="w640"  width="640" height="1" bgcolor="#ffffff"></td>
						</tr>

						<!-- pied de page -->
						<tr class="pagebottom">
							<td id="color_1_4_2_edit" class="w640"  width="640" bgcolor="#%color_4%">
								<table class="w640"  width="640" cellpadding="0" cellspacing="0" border="0" >
									<tbody>
										<tr><td colspan=3><input id="color_picker_1_4_2_edit" class="color" name="color_picker_1_4_2" onchange="updateColorEdit(\'color_1_4_2_edit\', \'color_picker_1_4_2_edit\');" value="%color_4%" /></td></tr>
										<tr>
											<td colspan="5" height="10"></td>
										</tr>
										<tr>
											<td class="w30"  width="30"></td>
											<td class="w580"  width="580" valign="top">
												<textarea name="center_1_2_2" id="tpl1_center_2_2_edit">%center_2%</textarea>
											</td>

											<td class="w30"  width="30"></td>
										</tr>
										<tr>
											<td colspan="5" height="10"></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td class="w640"  width="640" height="60"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>';

$contentEdit3 = '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:rgb(42, 55, 78)" >
	<tbody>
		<tr>
			<td id="color_1_1_3_edit" align="center" bgcolor="#%color_1%">
				<input id="color_picker_1_1_3_edit" class="color" name="color_picker_1_1_3" onchange="updateColorEdit(\'color_1_1_3_edit\', \'color_picker_1_1_3_edit\');" value="%color_1%" />
				<table  cellpadding="0" cellspacing="0" border="0">
					<tbody>
						<tr>
							<td class="w640"  width="640" height="10"></td>
						</tr>

						<tr>
							<td align="center" class="w640"  width="640" height="20"></td>
						</tr>
						<tr>
							<td class="w640"  width="640" height="10"></td>
						</tr>


						<!-- entete -->
						<tr class="pagetoplogo">
							<td id="color_1_2_3_edit" bgcolor="#%color_2%">
								<table cellpadding="0" cellspacing="0" border="0">
									<tbody>
										<tr><td colspan="3"><input id="color_picker_1_2_3_edit" class="color" name="color_picker_1_2_3" onchange="updateColorEdit(\'color_1_2_3_edit\', \'color_picker_1_2_3_edit\');" value="%color_2%" /></td></tr>
										<tr>
											<td class="w30"  width="30"></td>
											<td  class="w580"  width="580" valign="middle" align="left">
												<div class="pagetoplogo-content">
													<center><img  style="text-decoration: none; display: block; color:#476688; font-size:30px;" src="../img/logo.jpg" alt="Mon Logo"/></center>
												</div>
											</td> 
											<td class="w30"  width="30"></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<!-- separateur horizontal -->
						<tr>
							<td  class="w640"  width="640" height="1" bgcolor="#d7d6d6"></td>
						</tr>

						 <!-- contenu -->
						<tr class="content">
							<td id="color_1_3_3_edit" class="w640" class="w640"  width="640" bgcolor="#%color_3%">
								<input id="color_picker_1_3_3_edit" class="color" name="color_picker_1_3_3" onchange="updateColorEdit(\'color_1_3_3_edit\', \'color_picker_1_3_3_edit\');" value="%color_3%" />
								<table class="w640"  width="640" cellpadding="0" cellspacing="0" border="0">
									<tbody>
										<tr>
											<td  class="w30"  width="30"></td>
											<td  class="w580"  width="580">
												<!-- une zone de contenu -->
												<table class="w580"  width="580" cellpadding="0" cellspacing="0" border="0">
													<tbody>                                                            
														<tr>
															<td class="w580"  width="580">
																<textarea name="center_1_1_3" id="tpl1_center_1_3_edit">%center_1%</textarea>
															</td>
														</tr>
													</tbody>
												</table>
												<!-- fin zone -->                                                   
											</td>
											<td class="w30" class="w30"  width="30"></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>

						<!--  separateur horizontal de 15px de  haut-->
						<tr>
							<td class="w640"  width="640" height="1" bgcolor="#ffffff"></td>
						</tr>

						<!-- pied de page -->
						<tr class="pagebottom">
							<td id="color_1_4_3_edit" class="w640"  width="640" bgcolor="#%color_4%">
								<table class="w640"  width="640" cellpadding="0" cellspacing="0" border="0" >
									<tbody>
										<tr><td colspan=3><input id="color_picker_1_4_3_edit" class="color" name="color_picker_1_4_3" onchange="updateColorEdit(\'color_1_4_3_edit\', \'color_picker_1_4_3_edit\');" value="%color_4%" /></td></tr>
										<tr>
											<td colspan="5" height="10"></td>
										</tr>
										<tr>
											<td class="w30"  width="30"></td>
											<td class="w580"  width="580" valign="top">
												<textarea name="center_1_2_3" id="tpl1_center_2_3_edit">%center_2%</textarea>
											</td>

											<td class="w30"  width="30"></td>
										</tr>
										<tr>
											<td colspan="5" height="10"></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td class="w640"  width="640" height="60"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>';