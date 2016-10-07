/**
 * Created by kalen on 10/6/16.
 */
import { Button, Modal } from 'react-bootstrap'
import React from 'react'


class EditableUser extends React.Component{

	constructor(){
		super();
		this.state = {
			data: null,
			isDelete: false,
			showDeleteModal: false,
			showUpdateModal: false
		}
	}

	openDeleteModal(){
		this.setState({showDeleteModal: true});
	}

	openUpdateModal() {
		this.setState({showUpdateModal: true});
	}

	closeDeleteModal(){
		this.setState({showDeleteModal: false});
	}

	closeUpdateModal(){
		this.setState({showUpdateModal: false});
	}

	confirmDeleteModal(){
		this.setState({isDelete: true});
	}

	confirmUpdateModal(){
		let newData = {
			username: this.refs.username.value,
			name: this.refs.name.value,
			unit_info: this.refs.unit_info.value,
			mobile_number: this.refs.mobile_number.value,
			user_type: this.refs.user_type.value,
		};
		this.setState({
			data: newData,
			showUpdateModal: false
		});
	}

	getUserType(user_type) {
		switch (user_type){
			case "0": return "本科生";
			case "1": return "研究生";
			case "2": return "教师";
			default: return "未知"
		}
	}

	componentWillMount() {
		this.setState({
			data: this.props.data
		});
	}

	render() {
		if(this.state.isDelete){
			return <div></div>
		}

		return(
				<tr>
					<Modal show={this.state.showDeleteModal} onHide={this.closeDeleteModal.bind(this)}>
						<Modal.Header closeButton>
							<Modal.Title>警告</Modal.Title>
						</Modal.Header>
						<Modal.Body>
							<p>你确定要删除当前用户吗?</p>
						</Modal.Body>
						<Modal.Footer>
							<Button bsStyle="default" onClick={this.closeDeleteModal.bind(this)}>取消</Button>
							<Button bsStyle="danger" onClick={this.confirmDeleteModal.bind(this)}>确定</Button>
						</Modal.Footer>
					</Modal>
					<Modal show={this.state.showUpdateModal} onHide={this.closeUpdateModal.bind(this)}>
						<Modal.Header closeButton>
							<Modal.Title>修改信息</Modal.Title>
						</Modal.Header>
						<Modal.Body>
							<div className="form-group">
								<label className="form-label">学号(工号)</label>
								<input type="text" ref='username' className="form-control" defaultValue={this.state.data.username} />
							</div>
							<div className="form-group">
								<label className="form-label">姓名</label>
								<input type="text" ref='name' className="form-control" defaultValue={this.state.data.name} />
							</div>
							<div className="form-group">
								<label className="form-label">单位</label>
								<input type="text" ref='unit_info' className="form-control" defaultValue={this.state.data.unit_info} />
							</div>
							<div className="form-group">
								<label className="form-label">手机号</label>
								<input type="text" ref='mobile_number' className="form-control" defaultValue={this.state.data.mobile_number} />
							</div>
							<div className="form-group">
								<label className="form-label">用户类型</label>
								<select ref='user_type' className="form-control" defaultValue={this.state.data.user_type}>
									<option value="0">本科生</option>
									<option value="1">研究生</option>
									<option value="2">教师</option>
								</select>
							</div>
						</Modal.Body>
						<Modal.Footer>
							<Button bsStyle="default" onClick={this.closeUpdateModal.bind(this)}>取消</Button>
							<Button bsStyle="primary" onClick={this.confirmUpdateModal.bind(this)}>确定</Button>
						</Modal.Footer>
					</Modal>
					<td>{this.state.data.username}</td>
					<td>{this.state.data.name}</td>
					<td>{this.state.data.unit_info}</td>
					<td>{this.state.data.mobile_number}</td>
					<td>{this.getUserType(this.state.data.user_type)}</td>
					<td>
						<Button bsStyle="primary" onClick={this.openUpdateModal.bind(this)}>修改</Button>
						&nbsp;&nbsp;
						<Button bsStyle="danger" onClick={this.openDeleteModal.bind(this)}>删除</Button>
					</td>
				</tr>

		)

	}

}

export default EditableUser