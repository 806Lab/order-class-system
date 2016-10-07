/**
 * Created by kalen on 10/5/16.
 */
import React from 'react';
import AuthenticationMixin from '../../mixins/AuthenticationMixin';
import UserManagementAction from '../../actions/usermanagement-action';
import UserList from './usermanagement/userlist'
import {Pagination} from 'react-bootstrap'
import AMZ from 'amazeui-react'

const action = new UserManagementAction();
const LIMIT = 10;

const UserManagement = React.createClass({
	mixins: [ AuthenticationMixin ],
	getInitialState: function () {
		return {
			users: [],
			activePage: 1,
			totalPage: 1
		}
	},
	componentDidMount ()  {
		this.getUserList(1);
	},
	getUserList(page) {
		let that = this;
		let offset = (page - 1) * LIMIT;
		that.setState({users: [], totalPage: 1});
		action.getUserList({offset: offset}, (users, count) => {
			let totalPage = parseInt(count / LIMIT + 1);
			that.setState({users: users, totalPage: totalPage});
			window.scrollTo(0,0);
		})
	},
	onPageChange(eventKey){
		this.setState({activePage: eventKey});
		this.getUserList(eventKey);
	},
	render: function () {
		return (
			<div>
				<AMZ.FormGroup inline >
					<AMZ.Grid className="doc-g">
						<AMZ.Col sm={3}>
							<AMZ.Input type="select">
								<option value="0">默认显示</option>
								<option value="1">按学号搜索</option>
								<option value="2">按姓名搜索</option>
								<option value="3">按单位搜索</option>
							</AMZ.Input>
						</AMZ.Col>
						<AMZ.Col sm={5}>
							<AMZ.Input type="text" placeholder="搜索的内容" />
						</AMZ.Col>
						<AMZ.Col sm={4}>
							<AMZ.Button amStyle="primary">搜索</AMZ.Button>&nbsp;&nbsp;
							<AMZ.Button amStyle="secondary">添加用户</AMZ.Button>
						</AMZ.Col>
					</AMZ.Grid>
				</AMZ.FormGroup>

				<table className="table table-striped table-hover text-center">
					<thead>
					<tr>
						<th>学号(工号)</th>
						<th>姓名</th>
						<th>单位</th>
						<th>手机号</th>
						<th>用户类型</th>
						<th>操作</th>
					</tr>
					</thead>
					<UserList data={this.state.users} />
				</table>
				<center>
					<Pagination
						prev={true}
						next={true}
						first={true}
						last={true}
						items={this.state.totalPage}
						maxButtons={5}
						boundaryLinks={true}
						ellipsis={true}
						activePage={this.state.activePage}
						onSelect={this.onPageChange} />
				</center>
			</div>

		)
	}
});

module.exports = UserManagement;