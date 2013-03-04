tinyMCEPopup.requireLangPack();

var CmsRSSDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		f.count.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
	},

	insert : function() {
		// Insert the contents from the input into the document
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, '{{rss:' + document.forms[0].element_id.value + ':' + (isNaN(parseInt(document.forms[0].count.value)) ? 5 : document.forms[0].count.value) + '}}');
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(CmsRSSDialog.init, CmsRSSDialog);
