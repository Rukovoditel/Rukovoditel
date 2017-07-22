var is_app_caht_timer = false;

var app_caht_timer = false;

var app_caht_users_timer = false;

var app_caht_users_search = false;

var app_caht_selection_process = false;

function app_chat()
{
//scroll down messages list	
	this.scroll_msg_content = function()
	{		
		$('.chat-msg-content').scrollTop($('.chat-msg-content')[0].scrollHeight)
	}

//send messages by ctrl enter	
	this.send_msg_by_ctrl_enter = function()
	{
		$('#chat_message_text').keydown(function (e) {

			if((e.ctrlKey) && ((e.keyCode == 0xA)||(e.keyCode == 0xD))) 
		  {
				$('#chat-msg-form').submit()
		  }
		});	
	}

//send messages by enter	
	this.send_msg_by_enter = function()
	{
		$('#chat_message_text').keydown(function (e) {

			if((e.ctrlKey) && ((e.keyCode == 0xA)||(e.keyCode == 0xD))) 
		  {
				var selection = window.getSelection()
        ,range = selection.getRangeAt(0)
        ,br = document.createElement("br")
        ,textNode = document.createTextNode("\u00a0")
        ;
		    range.deleteContents();//required or not?
		    range.insertNode(br);
		    range.collapse(false);
		    range.insertNode(textNode);
		    range.selectNodeContents(textNode);
		    selection.removeAllRanges();
		    selection.addRange(range);
		    return false;
		  }
			else
			{
				if((e.keyCode == 0xA)||(e.keyCode == 0xD)) 
			  {
					$('#chat-msg-form').submit()
					
					return false;
			  }
			}			
		});	
	}		
}

/*
 * Update title if there count unread messages
 */
function app_chat_set_meta_title()
{
	count_unread_msg = $('#app-chat-button-count-unread').html().replace(/<\/?[^>]+>/gi, '');
	if(count_unread_msg.length>0)
	{		
		$('title').html('('+count_unread_msg+') '+app_meta_title)
	}
	else
	{
		$('title').html(app_meta_title)
	}
}