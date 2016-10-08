/**
 * Created by kalen on 10/5/16.
 */
import React from 'react';
import AuthenticationMixin from '../../mixins/AuthenticationMixin';
import UserManagementAction from '../../actions/usermanagement-action';
import UserList from './usermanagement/userlist'
import {Pagination, Modal, Button} from 'react-bootstrap'
import AMZ from 'amazeui-react'

const action = new UserManagementAction();
const LIMIT = 10;

const UserManagement = React.createClass({
	mixins: [ AuthenticationMixin ],
	getInitialState: function () {
		return {
			users: [],
			activePage: 1,
			totalPage: 1,
			showAddModal: false,
		}
	},
	componentDidMount ()  {
		this.searchUsers(1);
	},
	searchUsers(page) {
		let that = this;
		let offset = (page - 1) * LIMIT;
		let searchType = this.refs.search_type.getValue();
		let searchContent = this.refs.search_content.getValue();
		//清空数据
		// that.setState({users: [], totalPage: 1});
		//发送请求
		action.searchUsers({
			offset: offset,
			search_type: searchType,
			search_content: searchContent
		}, (users, count) => {
			let totalPage = parseInt(count / LIMIT + 1);
			that.setState({users: users, totalPage: totalPage});
			window.scrollTo(0,0);
		})
	},
	onPageChange(eventKey){
		this.setState({activePage: eventKey});
		this.searchUsers(eventKey);
	},
	openAddModal() {
		this.setState({showAddModal: true});
	},
	closeAddModal(){
		this.setState({showAddModal: false});
	},
	confirmAddModal(){
		let that = this;
		let newUser = {
			username: this.refs.new_username.value,
			name: this.refs.new_name.value,
			unit_info: this.refs.new_unit_info.value,
			mobile_number: this.refs.new_mobile_number.value,
			user_type: this.refs.new_user_type.value
		};
		action.addUser(newUser, ()=>{
			that.setState({
				showAddModal: false
			});
			that.onPageChange(that.state.totalPage);
		});
		
	},
	render: function () {
		return (
			<div>
				<Modal show={this.state.showAddModal} onHide={this.closeAddModal.bind(this)}>
					<Modal.Header closeButton>
						<Modal.Title>修改信息</Modal.Title>
					</Modal.Header>
					<Modal.Body>
						<div className="form-group">
							<label className="form-label">学号(工号)</label>
							<input type="text" ref='new_username' className="form-control" />
						</div>
						<div className="form-group">
							<label className="form-label">姓名</label>
							<input type="text" ref='new_name' className="form-control" />
						</div>
						<div className="form-group">
							<label className="form-label">单位</label>
							<input type="text" ref='new_unit_info' className="form-control" />
						</div>
						<div className="form-group">
							<label className="form-label">手机号</label>
							<input type="text" ref='new_mobile_number' className="form-control" />
						</div>
						<div className="form-group">
							<label className="form-label">用户类型</label>
							<select ref='new_user_type' className="form-control">
								<option value="0">本科生</option>
								<option value="1">研究生</option>
								<option value="2">教师</option>
							</select>
						</div>
					</Modal.Body>
					<Modal.Footer>
						<Button bsStyle="default" onClick={this.closeAddModal.bind(this)}>取消</Button>
						<Button bsStyle="primary" onClick={this.confirmAddModal.bind(this)}>确定</Button>
					</Modal.Footer>
				</Modal>
				<AMZ.FormGroup inline >
					<AMZ.Grid className="doc-g">
						<AMZ.Col sm={3}>
							<AMZ.Input type="select" ref="search_type" >
								<option value="0">默认显示</option>
								<option value="1">按学号搜索</option>
								<option value="2">按姓名搜索</option>
								<option value="3">按单位搜索</option>
							</AMZ.Input>
						</AMZ.Col>
						<AMZ.Col sm={5}>
							<AMZ.Input type="text" ref="search_content" placeholder="搜索的内容" />
						</AMZ.Col>
						<AMZ.Col sm={4}>
							<AMZ.Button amStyle="primary" onClick={(e)=> {this.onPageChange(1);}}>搜索</AMZ.Button>&nbsp;&nbsp;
							<AMZ.Button amStyle="secondary" onClick={this.openAddModal.bind(this)}>添加用户</AMZ.Button>
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