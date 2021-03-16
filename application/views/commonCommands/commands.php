<style type="text/css">
	.commandBox
	{
		border: 1px solid #ccc;
		border-radius: 20px;
		width: 100%;
    padding: 5px 15px;

	}
	.commandDetails{
		position:relative;

	}
	.commandDetails .sendCommand{
		position:absolute;
		right: 33px;
		top: -1px;
		cursor:pointer;
		
	}
</style>
<div class="row">
	<div class="col-md-12 commandDetails">
				<input type="text" class="commandBox" id="Commands" name="Commands" value="" placeholder="Write a Command ...">
				<i title="SendCommand" class="fa fa-paper-plane-o sendCommand" style="font-size: 20px; color: #e40a0a; margin-top: 10px;"></i>
	</div>
</div>