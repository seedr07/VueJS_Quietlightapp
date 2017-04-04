require('porter');
var localstore = require('cookies-js') 
var Emitter = require('events').EventEmitter
var store = module.exports = new Emitter()

var api = new Porter({
	addendum: {
		index: ['get', '/addendum'],
		get: ['get', '/addendum/:id'],
		destroy: ['delete', '/addendum/:id'],
		update: ['put', '/addendum/:id'],
		create: ['post', '/addendum']
	},
	config: {
		index: ['get', '/config'],
		get: ['get', '/config/:id'],
		destroy: ['delete', '/config/:id'],
		update: ['put', '/config/:id'],
		create: ['post', '/config']
	},
	disclosure: {
		index: ['get', '/disclosure'],
		get: ['get', '/disclosure/:id'],
		destroy: ['delete', '/disclosure/:id'],
		update: ['put', '/disclosure/:id'],
		create: ['post', '/disclosure']
	},
	executivesummary: {
		index: ['get', '/executivesummary'],
		get: ['get', '/executivesummary/:id'],
		destroy: ['delete', '/executivesummary/:id'],
		update: ['put', '/executivesummary/:id'],
		create: ['post', '/executivesummary']
	},
	financial: {
		index: ['get', '/financial'],
		get: ['get', '/financial/:id'],
		destroy: ['delete', '/financial/:id'],
		update: ['put', '/financial/:id'],
		create: ['post', '/financial']
	},
	financialv2: {
		index: ['get', '/financialv2'],
		get: ['get', '/financialv2/:id'],
		destroy: ['delete', '/financialv2/:id'],
		update: ['put', '/financialv2/:id'],
		create: ['post', '/financialv2']
	},
	flex: {
		index: ['get', '/flex'],
		get: ['get', '/flex/:id'],
		destroy: ['delete', '/flex/:id'],
		update: ['put', '/flex/:id'],
		create: ['post', '/flex']
	},
	footnote: {
		index: ['get', '/footnote'],
		get: ['get', '/footnote/:id'],
		destroy: ['delete', '/footnote/:id'],
		update: ['put', '/footnote/:id'],
		create: ['post', '/footnote']
	},
	question: {
		index: ['get', '/question'],
		get: ['get', '/question/:id'],
		destroy: ['delete', '/question/:id'],
		update: ['put', '/question/:id'],
		create: ['post', '/question']
	},
	questionbox: {
		index: ['get', '/questionbox'],
		get: ['get', '/questionbox/:id'],
		destroy: ['delete', '/questionbox/:id'],
		update: ['put', '/questionbox/:id'],
		create: ['post', '/questionbox']
	},
	session: {
		register: ['post', '/session/register'],
		login: ['post', '/session/login'],
		logout: ['delete', '/session'],
		user: {
			get: ['get', '/session/user/:id'],
			create: ['post', '/session/register'],
			index: ['get', '/session/user'],
			update: ['post', '/session/user/:id'],
			destroy: ['delete', '/session/user']
		}
	},
	summary: {
		index: ['get', '/summary'],
		get: ['get', '/summary/:id'],
		destroy: ['delete', '/summary/:id'],
		update: ['put', '/summary/:id'],
		create: ['post', '/summary']
	},
	summarysection: {
		index: ['get', '/summarysection'],
		get: ['get', '/summarysection/:id'],
		destroy: ['delete', '/summarysection/:id'],
		update: ['put', '/summarysection/:id'],
		create: ['post', '/summarysection']
	},
	title: {
		index: ['get', '/title'],
		get: ['get', '/title/:id'],
		destroy: ['delete', '/title/:id'],
		update: ['put', '/title/:id'],
		create: ['post', '/title']
	},
	webtraffic: {
		index: ['get', '/webtraffic'],
		get: ['get', '/webtraffic/:id'],
		destroy: ['delete', '/webtraffic/:id'],
		update: ['put', '/webtraffic/:id'],
		create: ['post', '/webtraffic']
	},
}).use({
	'protocol': 'http',
	'ip': __APIURL__,
	'host': __APIURL__
}).on({
  '401': function(err, response) {
  	window.location = "/#/logout";
  }
});

//Set session token, if user logged in
if(localstore.get('token')){
	api.headers['Session'] = localstore.get('token');
}

/**
 * Addendums
 */
store.getAddendums = function(limit, offset, cb) {
	// either function(cb) {} or function(....., cb){}
	cb = (typeof limit == "function") ? limit : cb;
	limit = (typeof limit == "number") ? limit : null;
	offset = (typeof offset == "number") ? offset :  null;
	

	api.addendum.index(cb);
}
store.getAddendum = function(id, cb) {
	api.addendum.get({id: id}, cb);
}
store.destroyAddendum = function(id, cb) {
	api.addendum.destroy({id: id}, cb);
}
store.updateAddendum = function(id, description, file, cb) {
	api.addendum.update({id: id}, {description: description, file: file}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createAddendum = function(summary_id, description, file, cb) {
	api.addendum.create({summary_id: summary_id, description: description, file: file}, cb);
}

/**
 * Configs
 */
store.getConfigs = function(limit, offset, summary_id, cb) {
	// either function(cb) {} or function(....., cb){}
	limit = (typeof limit == "number") ? limit : null;
	cb = (typeof limit == "function") ? limit : cb;
	offset = (typeof offset == "number") ? offset :  null;
	summary_id = (typeof summary_id == "number") ? summary_id :  summary_id;
	

	api.config.index(cb);
}
store.getConfig = function(id, cb) {
	api.config.get({id: id}, cb);
}
store.getConfigByKey = function(key, cb) {
	api.config.get({id: "find"},{key: key}, cb);
}
store.getLocalConfigByKey = function(summary_id, key, cb) {
	api.config.get({id: "find"},{summary_id: summary_id, key: key}, cb);
}
store.destroyConfig = function(id, cb) {
	api.config.destroy({id: id}, cb);
}
store.updateConfig = function(id, name, value, cb) {
	api.config.update({id: id}, {name: name, value: value}, cb);
}
store.createConfig = function(summary_id, name, value, cb) {
	api.config.create({summary_id: summary_id, name: name, value: value}, cb);
}

/**
 * Disclosures
 */
store.getDisclosures = function(limit, offset, cb) {
	// either function(cb) {} or function(....., cb){}
	cb = (typeof limit == "function") ? limit : cb;
	limit = (typeof limit == "number") ? limit : null;
	offset = (typeof offset == "number") ? offset :  null;
	

	api.disclosure.index(cb);
}
store.getDisclosure = function(id, cb) {
	api.disclosure.get({id: id}, cb);
}
store.destroyDisclosure = function(id, cb) {
	api.disclosure.destroy({id: id}, cb);
}
store.updateDisclosure = function(id, body, cb) {
	api.disclosure.update({id: id}, {body: body}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createDisclosure = function(summary_id, body, cb) {
	api.disclosure.create({summary_id: summary_id, body: body}, cb);
}

/**
 * Executive Summaries
 */
store.getExecutiveSummaries = function(limit, offset, cb) {
	// either function(cb) {} or function(....., cb){}
	cb = (typeof limit == "function") ? limit : cb;
	limit = (typeof limit == "number") ? limit : null;
	offset = (typeof offset == "number") ? offset :  null;
	

	api.executivesummary.index(cb);
}
store.getExecutiveSummary = function(id, cb) {
	api.executivesummary.get({id: id}, cb);
}
store.destroyExecutiveSummary = function(id, cb) {
	api.executivesummary.destroy({id: id}, cb);
}
store.updateExecutiveSummary = function(id, summary, benefits, total_revenue, discretionary_earnings, gross_profit, asking_price, inventory, inventory_footnote, inventory_included, show_multiple, cb) {
	api.executivesummary.update({id: id}, {summary: summary, benefits: benefits, total_revenue: total_revenue, discretionary_earnings: discretionary_earnings, gross_profit: gross_profit, asking_price: asking_price, inventory: inventory, inventory_footnote: inventory_footnote, inventory_included: inventory_included, show_multiple: show_multiple}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createExecutiveSummary = function(summary_id, summary, benefits, total_revenue, discretionary_earnings, gross_profit, asking_price, inventory, inventory_footnote, inventory_included, show_multiple, cb) {
	api.executivesummary.create({summary_id: summary_id,summary: summary, benefits: benefits, total_revenue: total_revenue, discretionary_earnings: discretionary_earnings, gross_profit: gross_profit, asking_price: asking_price, inventory: inventory, inventory_footnote: inventory_footnote, inventory_included: inventory_included, show_multiple: show_multiple}, cb);
}

/**
 * Financials
 */
store.getFinancials = function(limit, offset, cb) {
	// either function(cb) {} or function(....., cb){}
	cb = (typeof limit == "function") ? limit : cb;
	limit = (typeof limit == "number") ? limit : null;
	offset = (typeof offset == "number") ? offset :  null;
	

	api.financial.index(cb);
}
store.getFinancial = function(id, cb) {
	api.financial.get({id: id}, cb);
}
store.destroyFinancial = function(id, cb) {
	api.financial.destroy({id: id}, cb);
}
store.updateFinancial = function(id, body, pl_link, cb) {
	api.financial.update({id: id}, {body: body, pl_link: pl_link}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createFinancial = function(summary_id, body, pl_link, cb) {
	api.financial.create({summary_id: summary_id, body: body, pl_link: pl_link}, cb);
}

/**
 * FinancialV2
 */
store.getFinancialV2s = function(limit, offset, cb) {
	// either function(cb) {} or function(....., cb){}
	cb = (typeof limit == "function") ? limit : cb;
	limit = (typeof limit == "number") ? limit : null;
	offset = (typeof offset == "number") ? offset :  null;

	api.financialv2.index(cb);
}
store.getFinancialV2 = function(id, cb) {
	api.financialv2.get({id: id}, cb);
}
store.destroyFinancialV2 = function(id, cb) {
	api.financialv2.destroy({id: id}, cb);
}
store.updateFinancialV2 = function(id, csvbody, output_template, graph1, graph2, graph1_data, graph2_data, table1, table2, cb) {
	api.financialv2.update({id: id}, {csvbody: csvbody, output_template: output_template, graph1: graph1, graph2: graph2, graph1_data: graph1_data, graph2_data: graph2_data, table1: table1, table2: table2}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createFinancialV2 = function(summary_id, csvbody, output_template, graph1, graph2, graph1_data, graph2_data, table1, table2, cb) {
	api.financialv2.create({summary_id: summary_id, csvbody: csvbody, graph1: graph1, graph2: graph2, graph1_data: graph1_data, graph2_data: graph2_data, table1: table1, table2: table2}, cb);
}

/**
 * Flexes
 */
store.getFlexes = function(limit, offset, cb) {
	// either function(cb) {} or function(....., cb){}
	cb = (typeof limit == "function") ? limit : cb;
	limit = (typeof limit == "number") ? limit : null;
	offset = (typeof offset == "number") ? offset :  null;
	

	api.flex.index(cb);
}
store.getFlex = function(id, cb) {
	api.flex.get({id: id}, cb);
}
store.destroyFlex = function(id, cb) {
	api.flex.destroy({id: id}, cb);
}
store.updateFlex = function(id, body,  orientation, cb) {
	api.flex.update({id: id}, {body: body, orientation: orientation}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createFlex = function(summary_id, body, orientation, cb) {
	api.flex.create({summary_id: summary_id, body: body, orientation: orientation}, cb);
}


/**
 * QuestionBox
 */
store.getQuestionBoxes = function(limit, offset, cb) {
	// either function(cb) {} or function(....., cb){}
	cb = (typeof limit == "function") ? limit : cb;
	limit = (typeof limit == "number") ? limit : null;
	offset = (typeof offset == "number") ? offset :  null;
	
	api.questionbox.index(cb);
}
store.getQuestionBox = function(id, cb) {
	api.questionbox.get({id: id}, cb);
}
store.destroyQuestionBox = function(id, cb) {
	api.questionbox.destroy({id: id}, cb);
}
store.updateQuestionBox = function(id, body, cb) {
	api.questionbox.update({id: id}, {body: body}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createQuestionBox = function(summary_id, body, cb) {
	api.questionbox.create({summary_id: summary_id, body: body}, cb);
}

/**
 * Footnotes
 */
store.getFootnotes = function(limit, offset, summary_id, cb) {
	// either function(cb) {} or function(....., cb){}
	limit = (typeof limit == "number") ? limit : null;
	cb = (typeof limit == "function") ? limit : cb;
	offset = (typeof offset == "number") ? offset :  null;
	summary_id = (typeof summary_id == "number") ? summary_id :  summary_id;
	
	
	api.footnote.index({summary_id: summary_id, limit:limit, offset: offset},cb);
}
store.getFootnote = function(id, cb) {
	api.footnote.get({id: id}, cb);
}
store.destroyFootnote = function(id, cb) {
	api.footnote.destroy({id: id}, cb);
}
store.updateFootnote = function(id, table, field, body, associated_id, cb) {
	api.footnote.update({id: id}, {table: table, field: field, body: body, associated_id: associated_id}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createFootnote = function(summary_id, table, field, body, associated_id, cb) {
	api.footnote.create({summary_id: summary_id, table: table, field: field, body: body, associated_id: associated_id}, cb);
}

/**
 * Questions
 */
store.getQuestions = function(limit, offset, cb) {
	// either function(cb) {} or function(....., cb){}
	cb = (typeof limit == "function") ? limit : cb;
	limit = (typeof limit == "number") ? limit : null;
	offset = (typeof offset == "number") ? offset :  null;
	

	api.question.index(cb);
}
store.getQuestion = function(id, cb) {
	api.question.get({id: id}, cb);
}
store.destroyQuestion = function(id, cb) {
	api.question.destroy({id: id}, cb);
}
store.updateQuestion = function(id, header, question, answer, cb) {
	api.question.update({id: id}, {header: header, question: question, answer: answer}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createQuestion = function(summary_id, header, question, answer, cb) {
	api.question.create({summary_id: summary_id, header: header, question: question, answer: answer}, cb);
}

/**
 * Session
 */
store.register = function(email, pass, cb) {
	api.session.register({email: email, password: pass}, cb);
}
store.login = function(email, pass, cb) {
	api.session.login({email: email, password: pass}, function(error, response){
		if("session" in response) {
			api.headers['Session'] = response.session;
			
			var user = response;
			if(user.is_admin == "1") user.is_admin = true;
			else user.is_admin = false;

			localstore.set('user', JSON.stringify(user));
			localstore.set('token', response.session);
		} else {
			store.emit('loginIncorrect')
		}
		if(cb) cb(error, response);
	});
}
store.logout = function(cb) {
	api.session.logout(function(error, response){
		localstore.expire('user');
		localstore.expire('token');
		delete api.headers['Session'];
		if(cb) cb(error, response);
		store.emit('logout');
	});
}
store.user = {
	get: function(id, cb) {
		api.session.user.get({id: id}, cb);
	},
	list: function(limit, offset, cb) {
		// either function(cb) {} or function(....., cb){}
		cb = (typeof limit == "function") ? limit : cb;
		limit = (typeof limit == "number") ? limit : null;
		offset = (typeof offset == "number") ? offset :  null;
		

		api.session.user.index({limit: limit, offset: offset}, cb);
	},
	create: function(email, password, first_name, last_name, phone, is_admin, cb ) {
		api.session.user.create({email: email, password: password, first_name: first_name, last_name: last_name, phone: phone, is_admin: is_admin}, cb);
	},
	update: function(id, email, password, first_name, last_name, phone, is_admin, cb ) {
		api.session.user.update({id: id}, {email: email, password: password, first_name: first_name, last_name: last_name, phone: phone, is_admin: is_admin}, function(error, response){
			curuser = (localstore.get('user')) ? JSON.parse(localstore.get('user')) : false;
			if(response.id == curuser.id) localstore.set('user', JSON.stringify(response));
			if(cb) cb(error, response);
		});
	},
	destroy: function(cb) {
		api.session.user.destroy(function(error, response){
			localstore.expire('user');
			if(cb) cb(error, response);
		});
	}
}

/**
 * Summarys
 */
store.getSummarys = function(limit, offset, cb) {
	// either function(cb) {} or function(....., cb){}
	cb = (typeof limit == "function") ? limit : cb;
	limit = (typeof limit == "number") ? limit : null;
	offset = (typeof offset == "number") ? offset :  null;
	

	api.summary.index({limit: limit, offset: offset}, cb);
}
store.getSummary = function(id, cb) {
	api.summary.get({id: id}, cb);
}
store.destroySummary = function(id, cb) {
	api.summary.destroy({id: id}, cb);
}
store.updateSummary = function(id, name, cb) {
	api.summary.update({id: id}, {name: name}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createSummary = function(name, cb) {
	api.summary.create({name: name}, cb);
}

/**
 * Summary Sections
 */
store.getSummarySections = function(limit, offset, summary_id, cb) {
	// either function(cb) {} or function(....., cb){}
	limit = (typeof limit == "number") ? limit : null;
	cb = (typeof limit == "function") ? limit : cb;
	offset = (typeof offset == "number") ? offset :  null;
	summary_id = (typeof summary_id == "number") ? summary_id :  summary_id;
	
	

	api.summarysection.index({summary_id: summary_id, limit:limit, offset: offset},cb);
}
store.getSummarySection = function(id, cb) {
	api.summarysection.get({id: id}, cb);
}
store.destroySummarySection = function(id, cb) {
	api.summarysection.destroy({id: id}, function(){
		store.emit('destroySection');
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.updateSummarySection = function(id, table, name, associated_id, weight, cb) {
	api.summarysection.update({id: id}, {table: table, name: name, associated_id: associated_id, weight: weight}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createSummarySection = function(summary_id, table, name, associated_id, weight, cb) {
	api.summarysection.create({summary_id: summary_id, table: table, name: name, associated_id: associated_id, weight: weight}, function(){
		store.emit('createSection');
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}

/**
 * Titles
 */
store.getTitles = function(limit, offset, cb) {
	// either function(cb) {} or function(....., cb){}
	cb = (typeof limit == "function") ? limit : cb;
	limit = (typeof limit == "number") ? limit : null;
	offset = (typeof offset == "number") ? offset :  null;
	

	api.title.index(cb);
}
store.getTitle = function(id, cb) {
	api.title.get({id: id}, cb);
}
store.destroyTitle = function(id, cb) {
	api.title.destroy({id: id}, cb);
}
store.updateTitle = function(id, name, tagline, asking_price, advisor_id, cb) {
	api.title.update({id: id}, {name: name, tagline: tagline, asking_price: asking_price, advisor_id: advisor_id}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createTitle = function(summary_id, name, tagline, asking_price, advisor_id, cb) {
	api.title.create({summary_id: summary_id, name: name, tagline: tagline, asking_price: asking_price, advisor_id: advisor_id}, cb);
}

/**
 * WebTraffics
 */
store.getWebTraffics = function(limit, offset, cb) {
	// either function(cb) {} or function(....., cb){}
	cb = (typeof limit == "function") ? limit : cb;
	limit = (typeof limit == "number") ? limit : null;
	offset = (typeof offset == "number") ? offset :  null;
	

	api.webtraffic.index(cb);
}
store.getWebTraffic = function(id, cb) {
	api.webtraffic.get({id: id}, cb);
}
store.destroyWebTraffic = function(id, cb) {
	api.webtraffic.destroy({id: id}, cb);
}
store.updateWebTraffic = function(id, body, cb) {
	api.webtraffic.update({id: id}, {body: body}, function(){
		store.emit('summaryUpdated');
		if(cb) cb();
	});
}
store.createWebTraffic = function(summary_id, body, cb) {
	api.webtraffic.create({summary_id: summary_id, body: body}, cb);
}