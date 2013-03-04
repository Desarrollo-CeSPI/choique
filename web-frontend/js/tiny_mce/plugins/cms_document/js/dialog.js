tinyMCEPopup.requireLangPack();

var CmsDocumentDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		f.description.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
	},

	insert : function() {
		// Insert the contents from the input into the document
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, '{{documento:' + document.forms[0].element_id.value + '|' + document.forms[0].description.value + '}}');
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(CmsDocumentDialog.init, CmsDocumentDialog);
