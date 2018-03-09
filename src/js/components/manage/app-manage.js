/**
 * Author kevinwang
 * 2015-12-6
 * manage-order page
 */


var React = require('react');
var Login = require('../auth/app-login');
var ManageStore = require('../../stores/app-manageStore.js');
var ManageAction = require('../../actions/manage-action.js');
var AuthenticationMixin = require('../../mixins/AuthenticationMixin.js');

var AMZ = require('amazeui-react');

var About = React.createClass({
  mixins: [ AuthenticationMixin ],

  getInitialState: function() {
    return {
      neworderList: ManageStore.getAdminNewOrder(),
      is_operated: 0,
			selected: {}
    }
  },
  componentDidMount: function() {
    ManageAction.getOrder(0, this.state.is_operated);
    ManageStore.addChangeListener(this._onChange)
  },
  componentWillUnmount: function() {
      ManageStore.removeChangeListener(this._onChange);
  },
  _onChange: function() {
    this.setState({
      neworderList: ManageStore.getAdminNewOrder()
    })
  },
  handleSelect(key) {
    this.setState({
      is_operated: parseInt(key)
    }); 
    ManageAction.getOrder(0, key);
  },
  handleSort(item) {
    var neworder = this.state.neworderList.sort(function (a, b) {
        if (a[item] > b[item]) {
          return 1;
        }
        if (a[item] < b[item]) {
          return -1;
        }
        /// a must be equal to b
        return 0;
      })
    this.setState({
      neworderList: neworder
    })
  },
	acceptMultiOrders() {
  	let that = this;
		swal({
				title: "确认操作？",
				text: "将要接受多个订单",
				type: "warning",
				showCancelButton: true,
				closeOnConfirm: false,
				showLoaderOnConfirm: true,
			},
			function(){
				setTimeout(function(){
					ManageAction.acceptMultiOrders({
						"selected": JSON.stringify(that.state.selected)
					});
					swal({title: "操作成功!",timer:800});
					that.state.selected = {};
				}, 200);
			});
	},
	rejectMultiOrders() {
		let that = this;
		swal({
				title: "确认操作？",
				text: "将要拒绝多个订单",
				type: "warning",
				showCancelButton: true,
				closeOnConfirm: false,
				showLoaderOnConfirm: true,
			},
			function(){
				setTimeout(function(){
					ManageAction.rejectMultiOrders({
						"selected": JSON.stringify(that.state.selected)
					});
					swal({title: "操作成功!",timer:800});
					that.state.selected = {};
				}, 200);
			});

	},
	cleanOutDatedOrders() {
  	let that = this;
		swal({
				title: "确认操作？",
				text: "将要清空过期的订单",
				type: "warning",
				showCancelButton: true,
				closeOnConfirm: false,
				showLoaderOnConfirm: true,
			},
			function(){
				setTimeout(function(){
					ManageAction.cleanOutDatedOrders();
					that.state.selected = {};
				}, 200);
			});

	},
  

  render () {
  	const that = this;
    var neworder = this.state.neworderList;
    var neworderList = neworder.map(function(item, i) {
      function handleAgree(e, i) {
        var feed = window.prompt("反馈（可不填)");
        var data = {
          order_id: e,
          status: 1,
          feedback: feed,
        };
        swal({   
          title: "确认操作？",   
          text: "将要同意申请",   
          type: "info",   
          showCancelButton: true,   
          closeOnConfirm: false,   
          showLoaderOnConfirm: true, 
        }, 
        function(){   
          setTimeout(function(){
          	ManageAction.handleOrder(data);
            swal({title: "操作成功!",timer:800});
						that.state.selected = {};
          }, 200); 
        });
        
      }
      function handleRefuse(e, i) {
        var feed = window.prompt("拒绝理由（可不填)");
        var data = {
          order_id: e,
          status: 2,
          feedback: feed
        };
        swal({   
          title: "确认操作？",   
          text: "将要拒绝申请",   
          type: "info",   
          showCancelButton: true,   
          closeOnConfirm: false,   
          showLoaderOnConfirm: true, 
        }, 
        function(){   
          setTimeout(function(){
          	ManageAction.handleOrder(data);
            swal({title: "操作成功!",timer:800});
						that.state.selected = {};
          }, 200); 
        });
        
      }
      function handleShowFeedback(feedback) {
          swal(feedback || '未填写反馈');    
      }
      function checkboxValueChanged(uid, e) {
				that.state.selected[uid] = e.target.checked;
				console.log(that.state.selected)
			}
      let status = '', textColor = '';

			switch(parseInt(item.status)) {
				case 0:
				status = '待审核';textColor = 'text-warning';break;
				case 1:
				status = '已通过';textColor = 'text-success';break;
				case 2:
				status = '未通过';textColor = 'text-danger';break;
				case 3:
				status = '已使用';textColor = 'text-info';break;

			}
			if (item.status == 0) {
				return <tr key={i}>
									<td>
										<input type="checkbox" onChange={checkboxValueChanged.bind(this, item.id)}/>
									</td>
									<td>{item.date}</td><td>{item.time}</td><td>{item.classroom}</td>
									<td>{item.reason}</td>
									<td>{item.user.name}</td>
									<td>{item.user.unit_info}</td>
									<td>
										<button className="btn-success" onClick={handleAgree.bind(this,item.id)}>同意</button>
										<button className="btn-warning" onClick={handleRefuse.bind(this,item.id)}>拒绝</button>
									</td>
								</tr>
			}else{
				return <tr key={i}>
									<td>{item.date}</td>
									<td>{item.time}</td>
									<td>{item.classroom}</td>
									<td>{item.reason}</td>
									<td>{item.user.name}</td>
									<td>{item.user.mobile_number}</td>
									<td>{item.user.unit_info}</td>
									<td className={textColor}>
											{status}
											<button type="" className="show-qrcode btn-info" onClick={handleShowFeedback.bind(this, item.feedback)}>查看反馈</button>
									</td>
							 </tr>
			}
    });
    return (
    	<div>
				<div className="top-buttons-div">
					<button className="top-btn btn btn-warning" onClick={this.cleanOutDatedOrders}>清空过期订单</button>
					<button className="top-btn btn btn-danger" onClick={this.rejectMultiOrders}>一键拒绝</button>
					<button className="top-btn btn btn-primary" onClick={this.acceptMultiOrders}>一键通过</button>
				</div>

        <AMZ.Tabs onSelect={this.handleSelect}>
          <AMZ.Tabs.Item eventKey="0" title="未处理">
            <table className="table table-striped table-hover text-center">
              <thead>
                <tr>
									<th> </th>
									<th>日期<span className="glyphicon glyphicon-eject" style={{cursor:"pointer",marginLeft:"5px"}} onClick={this.handleSort.bind(this,'date')}></span></th>
									<th>时间<span className="glyphicon glyphicon-eject" style={{cursor:"pointer",marginLeft:"5px"}} onClick={this.handleSort.bind(this,'time')}></span></th>
									<th>教室<span className="glyphicon glyphicon-eject" style={{cursor:"pointer",marginLeft:"5px"}} onClick={this.handleSort.bind(this,'classroom')}></span></th>
									<th>原因</th>
									<th>申请人</th>
									<th>单位</th>
									<th>操作<span className="glyphicon glyphicon-eject" style={{cursor:"pointer",marginLeft:"5px"}} onClick={this.handleSort.bind(this,'status')}></span></th>
								</tr>
              </thead>
              <tbody>
              {neworderList}
              </tbody>
            </table>
          </AMZ.Tabs.Item>
          <AMZ.Tabs.Item eventKey="1" title="已处理">
            <table className="table table-striped table-hover text-center">
              <thead>
                <tr><th>日期<span className="glyphicon glyphicon-eject" style={{cursor:"pointer",marginLeft:"5px"}} onClick={this.handleSort.bind(this,'date')}></span></th>
                <th>时间<span className="glyphicon glyphicon-eject" style={{cursor:"pointer",marginLeft:"5px"}} onClick={this.handleSort.bind(this,'time')}></span></th>
                <th>教室<span className="glyphicon glyphicon-eject" style={{cursor:"pointer",marginLeft:"5px"}} onClick={this.handleSort.bind(this,'classroom')}></span></th>
                <th>原因</th>
                <th>申请人</th>
								<th>手机号</th>
                <th>单位</th>
                <th>操作<span className="glyphicon glyphicon-eject" style={{cursor:"pointer",marginLeft:"5px"}} onClick={this.handleSort.bind(this,'status')}></span></th></tr>
              </thead>
              <tbody>
              {neworderList}
              </tbody>
            </table>
          </AMZ.Tabs.Item>
        </AMZ.Tabs>
      </div>
    	);
  }
});

module.exports = About;
