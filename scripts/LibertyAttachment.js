/* Dependencies: MochiKit Base Async, BitAjax.j  */
LibertyAttachment.preflightCheck = function( cform ){
	var f= $(cform);
	var cid = f.content_id.value;
	var t = f.title.value;
	if ( MochiKit.Base.isEmpty(cid) ){
		alert( "A group to upload has not be properly specified. Upload aborted." );
		return false;
	}else if( MochiKit.Base.isEmpty(t) ){
		alert( "The group's title is unknown. Something is wrong with the upload form contact the adminstrator. Upload aborted." );
		return false;
	}else{
		f['liberty_attachments[title]'].value = t;
	}
	return true;
};

LibertyAttachment.postflightCheck = function( form, d ){/* do nothing */};
