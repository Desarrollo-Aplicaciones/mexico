<?php

class AdminCustomerThreadsController extends AdminCustomerThreadsControllerCore
{
	public function postProcess()
	{
		if ($id_customer_thread = (int)Tools::getValue('id_customer_thread'))
		{
			if (($id_contact = (int)Tools::getValue('id_contact')))
				Db::getInstance()->execute('
					UPDATE '._DB_PREFIX_.'customer_thread
					SET id_contact = '.(int)$id_contact.'
					WHERE id_customer_thread = '.(int)$id_customer_thread
				);
			if ($id_status = (int)Tools::getValue('setstatus'))
			{
				$status_array = array(1 => 'open', 2 => 'closed', 3 => 'pending1', 4 => 'pending2');
				Db::getInstance()->execute('
					UPDATE '._DB_PREFIX_.'customer_thread
					SET status = "'.$status_array[$id_status].'"
					WHERE id_customer_thread = '.(int)$id_customer_thread.' LIMIT 1
				');
			}
			if (isset($_POST['id_employee_forward']))
			{
				$messages = Db::getInstance()->executeS('
					SELECT ct.*, cm.*, cl.name subject, CONCAT(e.firstname, \' \', e.lastname) employee_name,
						CONCAT(c.firstname, \' \', c.lastname) customer_name, c.firstname
					FROM '._DB_PREFIX_.'customer_thread ct
					LEFT JOIN '._DB_PREFIX_.'customer_message cm
						ON (ct.id_customer_thread = cm.id_customer_thread)
					LEFT JOIN '._DB_PREFIX_.'contact_lang cl
						ON (cl.id_contact = ct.id_contact AND cl.id_lang = '.(int)$this->context->language->id.')
					LEFT OUTER JOIN '._DB_PREFIX_.'employee e
						ON e.id_employee = cm.id_employee
					LEFT OUTER JOIN '._DB_PREFIX_.'customer c
						ON (c.email = ct.email)
					WHERE ct.id_customer_thread = '.(int)Tools::getValue('id_customer_thread').'
					ORDER BY cm.date_add DESC
				');
				$output = '';
				foreach ($messages as $message)
					$output .= $this->displayMessage($message, true, (int)Tools::getValue('id_employee_forward'));

				$cm = new CustomerMessage();
				$cm->id_employee = (int)$this->context->employee->id;
				$cm->id_customer_thread = (int)Tools::getValue('id_customer_thread');
				$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
				$current_employee = $this->context->employee;
				$id_employee = (int)Tools::getValue('id_employee_forward');
				$employee = new Employee($id_employee);
				$email = Tools::getValue('email');
				if ($id_employee && $employee && Validate::isLoadedObject($employee))
				{
					$params = array(
					'{messages}' => Tools::nl2br(stripslashes($output)),
					'{employee}' => $current_employee->firstname.' '.$current_employee->lastname,
					'{comment}' => stripslashes($_POST['message_forward']));

					if (Mail::Send(
						$this->context->language->id,
						'forward_msg',
						Mail::l('Fwd: Customer message', $this->context->language->id),
						$params,
						$employee->email,
						$employee->firstname.' '.$employee->lastname,
						$current_employee->email,
						$current_employee->firstname.' '.$current_employee->lastname,
						null, null, _PS_MAIL_DIR_, true))
					{
						$cm->private = 1;
						$cm->message = $this->l('Message forwarded to').' '.$employee->firstname.' '.$employee->lastname."\n".$this->l('Comment:').' '.$_POST['message_forward'];
						$cm->add();
					}
				}
				elseif ($email && Validate::isEmail($email))
				{
					$params = array(
					'{messages}' => Tools::nl2br(stripslashes($output)),
					'{employee}' => $current_employee->firstname.' '.$current_employee->lastname,
					'{comment}' => stripslashes($_POST['message_forward']));

					if (Mail::Send(
						$this->context->language->id,
						'forward_msg',
						Mail::l('Fwd: Customer message', $this->context->language->id),
						$params, $email, null,
						$current_employee->email, $current_employee->firstname.' '.$current_employee->lastname,
						null, null, _PS_MAIL_DIR_, true))
					{
						$cm->message = $this->l('Message forwarded to').' '.$email."\n".$this->l('Comment:').' '.$_POST['message_forward'];
						$cm->add();
					}
				}
				else
					$this->errors[] = '<div class="alert error">'.Tools::displayError('The email address is invalid.').'</div>';
			}
			if (Tools::isSubmit('submitReply'))
			{
				$ct = new CustomerThread($id_customer_thread);

				ShopUrl::cacheMainDomainForShop((int)$ct->id_shop);

				$cm = new CustomerMessage();
				$cm->id_employee = (int)$this->context->employee->id;
				$cm->id_customer_thread = $ct->id;
				
				$cm->message = Tools::getValue('reply_message');
				$cm->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
				if (isset($_FILES) && !empty($_FILES['joinFile']['name']) && $_FILES['joinFile']['error'] != 0)
					$this->errors[] = Tools::displayError('An error occurred during the file upload process.');
				elseif ($cm->add())
				{
					$file_attachment = null;
					if (!empty($_FILES['joinFile']['name']))
					{
						$file_attachment['content'] = file_get_contents($_FILES['joinFile']['tmp_name']);
						$file_attachment['name'] = $_FILES['joinFile']['name'];
						$file_attachment['mime'] = $_FILES['joinFile']['type'];
					}
					$params = array(
						'{reply}' => Tools::nl2br(Tools::getValue('reply_message')),
						'{link}' => Tools::url(
							$this->context->link->getPageLink('contact', true),
							'id_customer_thread='.(int)$ct->id.'&token='.$ct->token
						),
					);
					//#ct == id_customer_thread    #tc == token of thread   <== used in the synchronization imap
					$contact = new Contact((int)$ct->id_contact, (int)$ct->id_lang);
					if (Validate::isLoadedObject($contact))
					{
						$from_name = $contact->name[(int)$ct->id_lang];
						$from_email = $contact->email;
					}
					else
					{
						$from_name = null;
						$from_email = null;
					}
					
					if (
						Mail::Send(
							(int)$ct->id_lang,
							'reply_msg',
							sprintf(Mail::l('An answer to your message is available', $ct->id_lang), $ct->id, $ct->token),
							$params, 
							Tools::getValue('msg_email'), 
							null, 
							$from_email, 
							'Farmalisto México',
							$file_attachment, 
							null,
							_PS_MAIL_DIR_, 
							true,
							null,
							'contacto.farmalisto@farmalisto.com.mx',
							true,
							$contact->name
						)
					)
					{
						$ct->status = 'closed';
						$ct->update();
					}
					Tools::redirectAdmin(
						self::$currentIndex.'&id_customer_thread='.(int)$id_customer_thread.'&viewcustomer_thread&token='.Tools::getValue('token')
					);
				}
				else
					$this->errors[] = Tools::displayError('An error occurred. Your message was not sent. Please contact your system administrator.');
			}
		}

		return parent::postProcess();
	}
}
