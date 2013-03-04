tinyMCEPopup.requireLangPack();

var CmsMultimediaDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		f.description.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
	},

	insert : function() {
		// Insert the contents from the input into the document
    var clickeable = (document.forms[0].clickeable.checked) ? document.forms[0].clickeable.value : '';
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, '{{multimedia:' + document.forms[0].element_id.value + clickeable + '|' + document.forms[0].description.value + '}}');
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(CmsMultimediaDialog.init, CmsMultimediaDialog);
