/**
 * Author kevinwang
 * 2015-12-6
 * entry main page
 */

var React = require('react');
var ReactDOM = require('react-dom');
var Router = require('react-router');
var Route = Router.Route;
var RouteHandler = Router.RouteHandler;
var Link = Router.Link;


var APP = require('./components/app').APP;

var Logout = require('./components/auth/app-logout');
var Login = require('./components/auth/app-login');

var Index = require('./components/auth/app-index');
var About = require('./components/about/app-about');
var Dashboard = require('./components/dashboard/app-dashboard');
var Manage = require('./components/manage/app-manage');
var Editclass = require('./components/manage/app-editclass');
var Queryclass = require('./components/manage/app-queryclass');
var UserManagement = require('./components/manage/app-usermanagement');

var routes = (
  <Route handler={APP}>
    <Route name="/" handler={Index}/>
    <Route name="login" handler={Login}/>
    <Route name="logout" handler={Logout}/>
    <Route name="about" handler={About}/>
    <Route name="dashboard" handler={Dashboard}/>
    <Route name="manage" handler={Manage}/>
    <Route name="editclass" handler={Editclass}/>
    <Route name="queryclass" handler={Queryclass}/>
    <Route name="usermanagement" handler={UserManagement}/>
  </Route>
);

Router.run(routes, function (Handler) {
    ReactDOM.render(<Handler/>, document.getElementById('myapp'));
});
