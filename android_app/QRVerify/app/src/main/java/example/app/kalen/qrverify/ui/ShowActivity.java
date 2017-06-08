package example.app.kalen.qrverify.ui;

import android.content.DialogInterface;
import android.os.Bundle;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;

import org.json.JSONException;

import example.app.kalen.qrverify.R;
import example.app.kalen.qrverify.asyntask.HttpRequestTask;
import example.app.kalen.qrverify.interf.Task;
import example.app.kalen.qrverify.model.Order;
import example.app.kalen.qrverify.utils.HttpUtils;
import example.app.kalen.qrverify.utils.JsonParser;

/**
 * Created by kalen on 2017/6/8.
 */

public class ShowActivity extends AppCompatActivity {
    TextView tvInfo;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        // TODO Auto-generated method stub
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_show);
        Order order = (Order) getIntent().getSerializableExtra("order");
        final String guid = getIntent().getStringExtra("guid");
        tvInfo = (TextView) findViewById(R.id.tv_info);
        tvInfo.setText(
                "借用日期：" + order.getDate() + "\n"
                        + "借用教室：" + order.getRoom() + "\n"
                        + "借用时间：" + order.getTime() + "\n"
                        + "借用人：" + order.getUsername() + "\n"
                        + "借用人所属单位：" + order.getUnitInfo()
        );
        findViewById(R.id.btn_commit).setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                // TODO Auto-generated method stub

                new AlertDialog.Builder(ShowActivity.this)
                        .setTitle("结果")
                        .setMessage("信息确认无误？")
                        .setNegativeButton("确定", new DialogInterface.OnClickListener() {

                            @Override
                            public void onClick(DialogInterface dialog, int which) {
                                // TODO Auto-generated method stub
                                new HttpRequestTask(ShowActivity.this, guid, new Task() {

                                    @Override
                                    public void excute(String json) {
                                        // TODO Auto-generated method stub
                                        try {
                                            if (JsonParser.getCode(json) != 0) {
                                                new AlertDialog.Builder(ShowActivity.this)
                                                        .setTitle("结果")
                                                        .setMessage(JsonParser.getMsg(json))
                                                        .setNegativeButton("确定", null)
                                                        .create()
                                                        .show();
                                            }else {
                                                new AlertDialog.Builder(ShowActivity.this)
                                                        .setTitle("结果")
                                                        .setMessage("验证成功，二维码已使用")
                                                        .setNegativeButton("确定", new DialogInterface.OnClickListener() {

                                                            @Override
                                                            public void onClick(DialogInterface dialog, int which) {
                                                                // TODO Auto-generated method stub
                                                                finish();
                                                            }
                                                        })
                                                        .create()
                                                        .show();
                                            }
                                        } catch (JSONException e) {
                                            e.printStackTrace();
                                            Toast.makeText(ShowActivity.this, "内部错误，重新尝试", Toast.LENGTH_LONG).show();;
                                        }
                                    }

                                    @Override
                                    public String excuteBackGround(String guid)
                                            throws Exception {
                                        return HttpUtils.commitRequest(guid);
                                    }
                                }).execute();
                            }
                        })
                        .setPositiveButton("取消", null)
                        .create()
                        .show();
            }
        });
    }
}
