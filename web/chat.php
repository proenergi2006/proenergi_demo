<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$enk  	= decode($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang="en">
	<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI", "rating", "myGrid"), "css"=>array("jqueryUI", "rating"))); ?>
	<body class="skin-blue fixed">
		<link href="<?=BASE_URL?>/libraries/thirdparty/chat/css/chat.css" rel="stylesheet">
		<!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"> -->
		<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet"> -->
		<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.7/css/mdb.min.css" rel="stylesheet"> -->
		<link href="<?=BASE_URL?>/libraries/thirdparty/chat/dist/emojionearea.css" rel="stylesheet">
		<link rel="stylesheet" href="<?=BASE_URL?>/libraries/thirdparty/chat/assets/css/chat.css" media="none" onload="if(media!='all')media='all'">
		<?php include_once($public_base_directory."/web/layout/header.php"); ?>	
	    <div class="wrapper row-offcanvas row-offcanvas-left">
			<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
	        <aside class="right-side">
	        	<section class="content-header">
	        		<h1>Free Chat</h1>
	        	</section>
				<section class="content">
				    <div class="messaging">
				      	<div class="inbox_msg">
					        <div class="inbox_people">
					          	<div class="headind_srch">
						            <div class="srch_bar">
						            	<div class="col-md-10">
						                	<input type="text" id="keyword" class="search-bar" onkeyup="search_user()"placeholder="Cari Obrolan">
						                </div>
					                	<div class="col-md-2">
					                  		<button type="button" class="btn btn-sm btn-primary"> 
					                  			<i class="fa fa-search" aria-hidden="true"></i> 
					                  		</button>
					                	</div>
						            </div>
					          	</div>
					          	<div class="inbox_chat">
					            	<div id="list_chat_user1"></div>
					            	<div id="list_chat_user"></div>
					            	<div id="search_user" style="z-index: 5; position: absolute; background-color: white; width: 260px; max-height: 300px; overflow-y: scroll;border: 1px solid #999;border-top: none;border-left: none; top: 25%;" hidden></div>
					          	</div>
					        </div>
					        <div class="mesgs">
					        	<button type="button" id="btnClearChat" style="margin-bottom: 20px;" hidden>Clear Chat</button>
					          	<div class="msg_history" id="msg_history">
					            	<div id="history_chat"></div>
				          		</div>
					          	<div id="form_send_message">
					          		<div class="col-md-11">
						              	<div class="type_msg">
						                	<div class="input_msg_write" id="inp1" hidden>
						                  		<input type="text" class="write_msg" id="message_chat" onkeydown="listen()" placeholder="Ketik pesan.." />
						                	</div>
						                	<div id="inp0">
						                  		<input type="text" class="form-control" placeholder="Ketik pesan.." disabled="" />
						                	</div>
						                </div>
						            </div>
					                <div class="col-md-1">
					                	<div id="inp2" hidden>
					                		<button type="button" id="inp2" class="btn btn-primary" style="margin-left: -90px; margin-top: 20px; border-radius: 0;">Send</button>
					                	</div>
					                </div>
					            </div>
					        </div>
				      	</div>
				    </div>
				</section>
				<?php $con->close(); ?>
	            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
			</aside>
		</div>
		<!-- Bootstrap tooltips -->
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.4/umd/popper.min.js">
		</script>
		<!-- Bootstrap core JavaScript -->
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?=BASE_URL?>/libraries/thirdparty/chat/dist/emojionearea.js">
		</script>
		<script type="text/javascript" src="<?=BASE_URL?>/libraries/thirdparty/chat/js/chat.js"></script>
		<script>
			//Inisiasi 
			$(document).ready(function(){
			    var interval
			    var friend
			    list_chat_user()
			});
			//Updating and send message
			function list_chat_user() {
			    list = '';
			    $.ajax({
			        url: '<?=BASE_URL_CLIENT?>/chat/__get_list_user.php',
			    }).done(function (data) {
			    	$('#list_chat_user').empty()
			        var data = JSON.parse(data);
			        for (var i = 0; i < data.length; i++) {
			            var list = 
			            	'<div class="chat_list" id="list-'+data[i]['id_user']+'" style="background-color: '+(data[i]['is_read']=='1'?'#94bdf9':'')+';">'+
			                	'<div class="chat_people" style="cursor: pointer;" onclick="manage(\''+data[i]['id_user']+'\')">'+
				                	'<div class="chat_img">'+
						                '<img src="<?=BASE_URL?>/images/no_profile_image.jpg" alt="image" style="width: 40px; height: 40px; border-radius: 120%;">'+
						            '</div>'+
						            '<div class="chat_ib">'+
						                '<h5>'+
						                	data[i]['fullname']+
						                	(data[i]['is_read']=='1'?' <div id="notif-'+data[i]['id_user']+'" style="height: 5px; width: 5px; background-color: red; display: inline-block; border-radius: 120%;"></div>':'')+
						                	'<br/><small>'+data[i]['role_name']+'</small>'+
						                '</h5>'+
						                '<p style="text-align: right;"><a href="javascript:;" onclick="return removeUser('+data[i]['id_user']+');" style="font-size: 11px; color: red;"><i class="fa fa-trash"></i> Delete</a></p>'+
					                '</div>'+
				                '</div>'+
			                '</div>'
			               ;
			            $(list).appendTo('#list_chat_user');
			        }
			    });
			}
			function manage(chat_to, fullname=null, role_name=null) {
				if (fullname!=null && role_name!=null) {
					$('#list_chat_user').removeClass('blur-c')
				    $('#search_user').prop('hidden', true)
				    let base_url = '<?=BASE_URL_CLIENT?>'
				    var list = 
				    	'<div class="chat_list" id="list-'+chat_to+'">'+
				        	'<div class="chat_people" style="cursor: pointer;" onclick="manage(\''+chat_to+'\')">'+
				            	'<div class="chat_img">'+
					                '<img src="<?=BASE_URL?>/images/no_profile_image.jpg" alt="image" style="width: 40px; height: 40px; border-radius: 120%;">'+
					            '</div>'+
					            '<div class="chat_ib">'+
					                '<h5>'+
					                	fullname+
					                	'<br/><small>'+role_name+'</small>'+
					                '</h5>'+
					                '<p style="text-align: right;"><a href="javascript:;" onclick="return removeUser('+chat_to+');" style="font-size: 11px; color: red;"><i class="fa fa-trash"></i> Delete</a></p>'+
				                '</div>'+
				            '</div>'+
				        '</div>'+
				        `<script>
							function removeUser(chat_to) {
						        $.ajax({
							        url: '`+base_url+`/chat/__delete_chat.php',
							        type: 'POST',
							        data: {
							            chat_to: chat_to
							        }
							    }).done(function (response) {
							    	$('#list-`+chat_to+`').remove()
							    })
						    }
				        <\/script>`
				       ;
				    $(list).appendTo('#list_chat_user1');
					// $('.chat_list').css('background-color', 'none')
					$('#list_chat_user').removeClass('blur-c')
					// alvin
				}
			    // 
				$('#inp1').prop('hidden', false)
				$('#inp0').prop('hidden', true)
				$('#inp2').prop('hidden', false)
				$('#btnClearChat').prop('hidden', false)
				$('#btnClearChat').val(chat_to)
				$('#history_chat').empty()
				$('#list-'+chat_to).css('background-color', '#eee')
				$('#notif-'+chat_to).css('display', 'none')
			    this.friend = chat_to;
			    get_list_chat(chat_to, true)
			    clearInterval(this.intervalID);
			    this.intervalID = setInterval(function () {
			        get_list_chat(chat_to)
			    }, 500);
			}
			function get_list_chat(chat_to, top=false) {
			    $.ajax({
			        url: '<?=BASE_URL_CLIENT?>/chat/__get_list_chat.php',
			        type: 'POST',
			        data: {
			            chat_to: chat_to
			        }
			    }).done(function (response) {
			        $('#history_chat').html(response)
			        if (top==true) {
				        let element = document.getElementById("msg_history");
							element.scrollTop = element.scrollHeight;
					}
			    });
			}
			function send_message(message) {
			    var chat_to = this.friend;
			    var message = message;
			    $.ajax({
			        url: '<?=BASE_URL_CLIENT?>/chat/__post_chat.php',
			        type: 'POST',
			        data: {
			            chat_to: chat_to,
			            message: message
			        }
			    }).done(function (response) {
			        manage(chat_to)
			    });
			}
			function search_user() {
			    var keyword = $('#keyword').val();
			    clearInterval(this.intervalID);
			    // $('#list_chat_user').text('');
			    // $('#list_chat_user').prop('hidden', true);
			    $('#list_chat_user').addClass('blur-c')
			    $('#history_chat').text('');
			    $('#search_user').empty()
			    $('#search_user').prop('hidden', false)
			    if (keyword == '') {
			        // list_chat_user();
			        $('#list_chat_user').removeClass('blur-c')
			    	$('#search_user').prop('hidden', true)
			    } else {
			        list = '';
			        $.ajax({
			            url: '<?=BASE_URL_CLIENT?>/chat/__get_user.php',
			            type: 'POST',
			            data: {
			                keyword: keyword
			            }
			        }).done(function (data) {
			            var data = JSON.parse(data);
			            for (var i = 0; i < data.length; i++) {
			                var list = 
				                '<div class="chat_list" id="list-'+data[i]['id_user']+'">'+
				                    '<div class="chat_people" style="cursor: pointer;" onclick="manage(\''+data[i]['id_user']+'\', \''+data[i]['fullname']+'\', \''+data[i]['role_name']+'\')">'+
					                    '<div class="chat_img">'+
					                    	'<img src="<?=BASE_URL?>/images/no_profile_image.jpg" alt="image" style="width: 40px; height: 40px; border-radius: 120%;">'+
					                    '</div>'+
					                    '<div class = "chat_ib" >'+
					                    	'<h5>'+
					                    		data[i]['fullname']+
					                    		'<br/><small>'+data[i]['role_name']+'</small>'+
					                    	'</h5>'+
					                    	'<p></p>'+
					                    '</div>'+
				                    '</div>'+
			                    '</div>'
			                    ;
			                $(list).appendTo('#search_user');
			            }
			        });
			    }
			}
			$("#message_chat").emojioneArea({
			    pickerPosition: "top",
			    events: {
			        keydown: function (editor, event) {
			            if (event.keyCode == 13) {
			                send_message(this.getText())
			                this.setText('')
			            }
			        }
			    }
			});
			$("#inp2").on('click', function() {
				let val = $('#message_chat').val()
				send_message(val)
				$(".emojionearea-editor").html('');
				$('#message_chat').val('')
				$('#message_chat').text('')
			})
			$("#btnClearChat").on('click', function() {
				let chat_to = $(this).val()
				$.ajax({
			        url: '<?=BASE_URL_CLIENT?>/chat/__delete_chat.php',
			        type: 'POST',
			        data: {
			            chat_to: chat_to
			        }
			    }).done(function (response) {
			        manage(chat_to)
			    })
			})
			function removeUser(chat_to) {
		        $.ajax({
			        url: '<?=BASE_URL_CLIENT?>/chat/__delete_chat.php',
			        type: 'POST',
			        data: {
			            chat_to: chat_to
			        }
			    }).done(function (response) {
			        manage(chat_to)
			        list_chat_user()
			    })
		    }
		</script>
	</body>
</html>      
