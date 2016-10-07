/**
 * Created by kalen on 10/6/16.
 */
import { Button, Modal } from 'react-bootstrap'
import React from 'react'


let labelStyle = {
    width: "85px",
    textAlign: "left"
};

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

	getUserType() {
		switch (this.props.data.user_type){
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
					<td>{this.state.data.username}</td>
					<td>{this.state.data.name}</td>
					<td>{this.state.data.unit_info}</td>
					<td>{this.state.data.mobile_number}</td>
					<td>{this.getUserType()}</td>
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