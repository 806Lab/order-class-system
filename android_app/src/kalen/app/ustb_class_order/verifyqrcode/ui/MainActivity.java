package kalen.app.ustb_class_order.verifyqrcode.ui;

import org.json.JSONException;

import kalen.app.ustb_class_order.verifyqrcode.R;
import kalen.app.ustb_class_order.verifyqrcode.interf.Task;
import kalen.app.ustb_class_order.verifyqrcode.utils.HttpUtils;
import kalen.app.ustb_class_order.verifyqrcode.utils.JsonParser;
import kalen.app.ustb_class_order.verifyqrcode.asynctask.HttpRequestTask;

import com.zxing.activity.CaptureActivity;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.Toast;

public class MainActivity extends Activity implements OnClickListener {

	private Button btnScan;
	private String guid;
	
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);

		btnScan = (Button) findViewById(R.id.btnScan);
		btnScan.setOnClickListener(this);
	}

	@Override
	public void onClick(View v) {
		Intent intent = new Intent(this, CaptureActivity.class);
		startActivityForResult(intent, 0);
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		if (resultCode == Activity.RESULT_OK) {
			String guidStr = data.getExtras().getString("result");
			guid = guidStr;
			new HttpRequestTask(MainActivity.this, guid, new Task() {
				
				@Override
				public void excute(String json) {
					// TODO Auto-generated method stub
					try {
						if (JsonParser.getCode(json) != 0) {
							new AlertDialog.Builder(MainActivity.this)
								.setTitle("发生意外")
								.setMessage(JsonParser.getMsg(json))
								.setNegativeButton("确定", null)
								.create()
								.show();
						}else {
							Intent intent = new Intent(MainActivity.this, ShowActivity.class);
							intent.putExtra("order", JsonParser.getOrder(json));
							intent.putExtra("guid", guid);
							startActivity(intent);
						}
					} catch (JSONException e) {
						e.printStackTrace();
						Toast.makeText(MainActivity.this, "请求错误", Toast.LENGTH_LONG).show();
					}
				}

				@Override
				public String excuteBackGround(String guid) throws Exception {
					return HttpUtils.getInfo(guid);
				}
			}).execute();
		}
	}

}
