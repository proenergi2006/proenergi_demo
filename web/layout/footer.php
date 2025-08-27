<!--<div style="position: absolute;top: 125px;background-color: yellow;z-index: 999;width: 220px;border: 1px solid;height: 564px;left: 200px;">&nbsp;</div>-->
<section class="main-footer">
	<div class="row">
		<div class="col-sm-12">
			<p class="text-right">PRO ENERGI &copy; <?= date('Y'); ?> - All Rights Reserved | Version 3.1.0</p>
		</div>
	</div>
</section>
<?php $_SESSION["sinori" . SESSIONID]["timeout"] = time(); ?>

<script type="text/javascript">
	function get_notification_chat() {
		$.ajax({
			url: '<?= BASE_URL_CLIENT ?>/chat/__get_notification_chat.php',
			type: 'GET'
		}).done(function(response) {
			// console.log(response)
			response = parseInt(response)
			if (Number.isInteger(response)) {
				$('#chat_notif').text(response)
				$('#chat_notif').css('background-color', '')
				if (response != 0)
					$('#chat_notif').css('background-color', 'red')
				let x = document.title
				x = x.split(') ')
				if (x.length == 1) {
					x = x[0]
				} else {
					x = x[1]
				}
				let _x = '(' + response + ') '
				if (response == 0) _x = ''
				document.title = _x + x
			}
		});
	}
</script>