package example.app.kalen.qrverify.asyntask;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.widget.Toast;

import example.app.kalen.qrverify.interf.Task;

/**
 * Created by kalen on 2017/6/8.
 */

public class HttpRequestTask extends AsyncTask<Void, Void, String> {

    String guid;
    Task task;
    Context context;
    ProgressDialog progressDialog;

    public HttpRequestTask(Context context, String guid, Task task) {
        // TODO Auto-generated constructor stub
        this.context = context;
        this.guid = guid;
        this.task = task;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        progressDialog = ProgressDialog.show(context,
                "请等待...", "正在请求网络数据...", true, false);
    }

    @Override
    protected String doInBackground(Void... params) {
        try {
            return task.excuteBackGround(guid);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        progressDialog.dismiss();
        if (result != null) {
            System.out.println(result);
            task.excute(result);
        }else {
            Toast.makeText( context, "加载失败，请检查网络配置", Toast.LENGTH_SHORT).show();
        }
    }

}