<!DOCTYPE html>
<html>
	<head>
		<title>Rize DB</title>
		<!-- Disable browser caching of dialog window -->
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="pragma" content="no-cache" />
 
		<style type="text/css">
			#selectme { font-size: 1.2em; color: #333; }
			.hover, .hover td { 
				background-color: #FFFF00 !important; 
				cursor:pointer;
			}
			table.gridtable {
				font-family: verdana,arial,sans-serif;
				font-size:11px;
				color:#333333;
				border-width: 1px;
				border-color: #666666;
				border-collapse: collapse;
			}
			table.gridtable th {
				border-width: 1px;
				padding: 8px;
				border-style: solid;
				border-color: #666666;
				background-color: #dedede;
			}
			table.gridtable td {
				border-width: 1px;
				padding: 8px;
				border-style: solid;
				border-color: #666666;
				background-color: #ffffff;
			}
		</style>

	</head>
	<body>
 		<table class='gridtable' id="itemTable" border="1">
 			<tr>
 				<th>Img</th>
 				<th>ID</th>
 				<th>Type</th>
 				<th>Name</th>
 				<th>Twitter</th>
 				<th>LinkedIn</th>
 				<th>Description</th>
 			</tr>
 		</table>
		<script type="text/javascript">
			//	Get Parent jQuery Variable
			var args = top.tinymce.activeEditor.windowManager.getParams();
			var $ = args['jquery'];
			var context = document.getElementsByTagName("body")[0];
			var editor = args['editor'];
				
			$(document).ready(function() {

				function insertContentInParent(txt) {
					editor.selection.setContent(txt);
					top.tinymce.activeEditor.windowManager.close();
				}

				textVal=$("itemTable", context).focus();
				function rowClick() {
					var rowID=$(this,context).attr("data-id");
					var o = rowData[rowID];
					var selectedText = editor.selection.getContent();	

					var returnText="";
					if (selectedText == "") {
						returnText += "[rizeDB id=" + rowID + "]" + o.name + "[/rizeDB]";
					} else {
						returnText += "[rizeDB id=" + rowID + "]" + selectedText + "[/rizeDB]";
					}				

					insertContentInParent(returnText);
				}

				function rowHover() { $(this, context).addClass('hover'); }
				function rowUnhover() { $(this, context).removeClass('hover'); }

				function success(data) {
					rowData={};
					for (var i=0; i < data.length; i++) {
						rowData[data[i].id]=data[i];
					}
					for (i=0; i<data.length; i++) {
						//console.log(data[i].name);
						var o = data[i];
						/*
						if (!o.hasOwnProperty("linkedIn")) {
							o.linkedIn = "defLinked";
						}*/
						rowStr="<tr data-id='" + o.id + "'><td><img border='1' width='25' height='25' src='"+o.image+"'></td><td>" + o.id + "</td><td>" + o.type + "</td><td>" + o.name + "</td><td>"+o.twitter+"</td><td>"+o.linkedIn+"</td><td>"+o.description+"</td></tr>";
						$('#itemTable tr:last', context).after(rowStr);
					}
					//add this click handler AFTER rows are populated else it doesn't pick up new rows
					$("#itemTable tr", context).click(rowClick);
					$("#itemTable tr", context).hover( rowHover, rowUnhover );

				}

				var apiURL="/wp-content/themes/independent-publisher-child-theme/rizeAPI.php";
				if (window.location.hostname == "wprize") {
					apiURL = "http://wprize/wprize/"+apiURL;
				} else if (window.location.hostname == "localhost") {
					apiURL = "http://localhost/wordpress/"+apiURL;
				}

				$.ajax({
					dataType: "json",
					url: apiURL,
					success: success,
					context:context
				});


			});
		</script>
		
	</body>
</html>