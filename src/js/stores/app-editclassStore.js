/**
 * Author kevinwang
 * 2015-12-6
 * edit-class store
 */

var AppDispatcher = require('../dispatchers/app-dispatcher');
var AppConstants = require('../constants/app-constants');
var EventEmitter = require('events').EventEmitter;
var React = require('react');
var update = require('react-addons-update');

var CHANGE_EVENT = "change";


var _date = [];
var _firstDate = null;
var _arrangement = [];



var AppStore = update(EventEmitter.prototype, {$merge: {
  emitChange:function(){
    this.emit(CHANGE_EVENT);
  },

  addChangeListener:function(callback){
    this.on(CHANGE_EVENT, callback)
  },

  removeChangeListener:function(callback){
    this.removeListener(CHANGE_EVENT, callback)
  },
  setAdminDate: function(params){
    _date = params;
    _firstDate = params[0].id;
  },
  getAdminDate: function() {
    return _date;
  },
  setArrangement: function(params) {
    _arrangement = params;
  },
  getArrangement: function() {
    return _arrangement;
  },
  getFirstDate: function(){
    return _firstDate;
  },


  dispatcherIndex:AppDispatcher.register(function(payload){
    var action = payload.action; // this is our action from handleViewAction
    switch(action.actionType){
      case AppConstants.GET_ADMIN_DATE:
      AppStore.setAdminDate(action.data);
      break;
      case AppConstants.GET_ARRANGEMENT:
      AppStore.setArrangement(action.data);
      break;

    }
    AppStore.emitChange();

    return true;
  })
}});

module.exports = AppStore;      