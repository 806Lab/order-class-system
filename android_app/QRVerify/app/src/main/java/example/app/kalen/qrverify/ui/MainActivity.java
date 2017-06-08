package example.app.kalen.qrverify.ui;

import android.content.Intent;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.Toast;

import com.google.zxing.integration.android.IntentIntegrator;
import com.google.zxing.integration.android.IntentResult;

import org.json.JSONException;

import example.app.kalen.qrverify.R;
import example.app.kalen.qrverify.asyntask.HttpRequestTask;
import example.app.kalen.qrverify.interf.Task;
import example.app.kalen.qrverify.utils.HttpUtils;
import example.app.kalen.qrverify.utils.JsonParser;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        findViewById(R.id.btn_scan).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                new IntentIntegrator(MainActivity.this).initiateScan();
            }
        });
    }


    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        IntentResult result = IntentIntegrator.parseActivityResult(requestCode, resultCode, data);
        if(result != null) {
            if(result.getContents() == null) {
                Toast.makeText(this, "已取消", Toast.LENGTH_LONG).show();
            } else {
                final String guid = result.getContents();
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
        } else {
            super.onActivityResult(requestCode, resultCode, data);
        }
    }
}
