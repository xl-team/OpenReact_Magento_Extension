var React = Class.create({
	url: false,
	html: false,
	canDisplay: true,
	
	initialize: function(args){
		this.clearSessionUrl = args.clearSessionUrl;	
		this.translations = args.translations;
		this.varName = args.varName;
		this.authenticateUrl = args.authenticateUrl;
		this.modeVar = args.modeVar;
		this.emailPostUrl = args.emailPostUrl;
		this.postMessageUrl = args.postMessageUrl;
		this.shareUrl = args.shareUrl;
	},
	
	saveEmail: function() {
		if (reactEmailForm.validator.validate()) {
			var email = $('react_email').value;	
			if (email.strip()) {
				this.request(this.emailPostUrl+'email/'+email);
			}
		}
	},
	
	authenticate: function() {
		var provider = $$('input[name="social_network"]:checked')[0];	
		if(provider) {
			this.request(this.authenticateUrl+'provider/'+provider.value);
		}
	},
	
	share: function() { 
		var react_form = document.createElement('form');
		for (i=0; i<4; i++)
		{
			var react_input = document.createElement('input');
			var source_input = $$('#react_share input')[i];
			react_input.value = source_input.value;
			react_input.name = source_input.name.replace(/react_/gi,'');
			react_form.appendChild(react_input);
		}
		this.request(this.shareUrl, react_form.serialize(true));
	},
	
	postMessage: function(){
		var message = {message: $('react_message').value};
		if(message.message.strip() !== ''){
			this.request(this.postMessageUrl, message);
		}
			
	},
	
	initBox: function(html){
		if(html.strip() !==  ''){
			$('react_dialogbox').update(html.strip());
			$('react_dialogbox').setStyle({height: 'auto'});
			if(this.canDisplay){
				$('react_dialogbox').style.top = '100px';
				$('react_dialogbox').style.left = '50%';
				new Effect.Appear($('react_overlay'), {duration: 0.6, from: 0, to: 1.0});	
				new Effect.Appear($('react_modalbox'), {duration: 0.6, from: 0, to: 1.0});
				this.canDisplay = false;
			}
		}
	},
	
	showLoader: function(){
		this.initBox('<div id="react_box_loader"></div>');	
	},
	
	hideLoader: function(){
		new Effect.Fade($('react_box_loader'), {duration: 0});		
	},
	
	clearSession: function()
	{
		this.showLoader();
		new Ajax.Request(this.clearSessionUrl, {
			onSuccess: function() {
				/*new Effect.Fade($('react_dialogbox'), {duration: 0.6});*/
				new Effect.Fade($('react_modalbox'), {duration: 0.6});
				$('react_dialogbox').style.top = '-10000px';
				$('react_dialogbox').style.left = '-10000px';
			}
		});
		this.canDisplay = true;
	},
	
	request: function(url, params){
		var self = this;
		var ajax_params = {}
		if(typeof(parmas) !== undefined)
		{
			ajax_params = params; 
		}
		if(url[url.length-1] != '/')
		{
			url = url+'/';
		}
		this.showLoader();
		new Ajax.Request(url+this.modeVar+'/1', {
			parameters: ajax_params,
			onSuccess: function(data) {
			var response = data.responseJSON;
				if(response.url) {
					if(typeof(response.url) == 'boolean'){
						window.location.reload();
					}
					else {
						window.location.href = response.url;	
					}
				}
				else if (response.html){
					self.initBox(response.html);	
					if($$('ul.messages').size())
					{
						$('react_dialogbox').insert({'top': $$('ul.messages')[0]});
					}
				}
				else {
					self.clearSession();	
				}
			}
		});
	}	
});