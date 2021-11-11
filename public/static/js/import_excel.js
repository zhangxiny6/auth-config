//得到父页面的excel数据
var win=art.dialog.opener;
var excelArr = win.getExcelData();


//操作对象
var excelOperationVue = new Vue({
    el:'#excelOperationVue',
    data:{
        header_config:{

        },
        upload_data:{
            curr_speed:0,   //当前进度
            result_data:[],  //结果
        },
        add_column:{
            //新增的临时存储数据
            column_data:{
                column_name:'',
                table_name:'',
                is_unique:'',
                position:''
            },
            is_add_column:false,    //是否处于添加状态
        },  //添加数据时用
        curr_top_x:0,   //当前头部x
        curr_top_y:0,   //当前行y
        curr_x:0,   //当前td标签x
        curr_y:0,   //当前td标签y
        import_data_type:import_data_type,  //当前导入的类别
        excelData:[],
        headerData:[],
        masterConfig:[],    //导入主配置
        columnConfig:[],    //导入字段配置
        currColumnName:'',  //当前选中的字段名
        is_show_column:true,    //是否显示字段
        progress_win:null,  //进度窗口
    },
    mounted:function () {
        this.$nextTick(function() {
            //初始化头部
            this.formatTableHeader();
            //得到导入配置
            this.getImportConfig();
        });
    },
    methods:{
        //格式化表头
        formatTableHeader:function(){
            var _this = this;
            setTimeout(function(){
                //延迟赋值
                _this.excelData = excelArr;

                if(_this.excelData!=null){
                    var header_length = _this.excelData[1].length;
                    for(var i=1;i<header_length+1;i++){
                        _this.headerData.push(String.fromCharCode(64 + parseInt(i)));
                    }
                }
                var pageHg = $(window).height(); //页面的高度
                var pageWidth = $(window).width(); //页面的高度

                var girdOffTopHg = 120; //距离顶部的高度
                var pageMaxGirdTableHg = pageHg - girdOffTopHg;
                $('.excel-table tbody').css('height', pageMaxGirdTableHg);
                $('.excel-table tbody').css('width', pageWidth);
            },300);
        },
        //点击表格
        clickItem:function (index,son_index) {
            //清除现在的td焦点
            this.clearTdFocus();

            //选中的得到焦点
            $("#"+index+'_'+son_index).addClass('item_active');
            $("#y_"+index).addClass('index_active');
            $("#x_"+son_index).addClass('index_active');
            this.curr_y = index;
            this.curr_x = son_index;

            //选中元素是否为要导入的内容，若为，则显示把内容字段配置
            if(this.curr_y>=this.masterConfig.data_first_pos&&(this.curr_x+1)>=this.masterConfig.column_first_pos){

            }
        },
        //得到导入配置
        getImportConfig:function(){
            var data = {
                import_data_type:this.import_data_type
            };
            $.ajax({
                data:data,
                url:getImportConfigUrl,
                dataType:'json',
                Type:'POST',
                success:function(result){
                    if(result['code']==0){
                        art.dialog.alert(result.msg, null);
                    }else{
                        var data = result.data;
                        excelOperationVue.masterConfig = data.masterConfig;
                        excelOperationVue.columnConfig = data.columnConfig;
                        if(data.columnConfig.length>0){
                            excelOperationVue.currColumnName = data.columnConfig[0]['column_name'];
                        }
                    }
                }, error:function(){
                    art.dialog.alert('网络请求失败,请稍后重试!', null);
                }
            })
        },
        //点击头部
        clickHeader:function (index) {
            //清除现在的td焦点
            this.clearTdFocus();
            $("[_x="+this.curr_top_x+"]").removeClass('item_selected');
            $("[_y="+this.curr_top_y+"]").removeClass('item_selected');
            $("[_x="+index+"]").addClass('item_selected');
            this.curr_top_y = null;
            this.curr_top_x = index;
        },
        //点击左列
        clickLeft:function (index) {
            //清除现在的td焦点
            this.clearTdFocus();
            $("[_y="+this.curr_top_y+"]").removeClass('item_selected');
            $("[_x="+this.curr_top_x+"]").removeClass('item_selected');
            $("[_y="+index+"]").addClass('item_selected');
            this.curr_top_x = null;
            this.curr_top_y = index;
        },
        //删除当前选中的行\列
        deleteCurrLine:function () {
            var curr_top_x = this.curr_top_x;
            var curr_top_y = this.curr_top_y;
            //删除列数据
            if(curr_top_x!=null){
                $.each(this.excelData,function (index,item) {
                    for(var son_index in item){
                        var son_item = item[son_index];
                        if(son_index==curr_top_x){
                            item.splice(son_index,1);
                        }
                    }
                });
                //删除头部最后一个
                this.headerData.splice(this.headerData.length-1,1);
            }
            //删除行数据
            else if(curr_top_y!=null){
                for(var index in this.excelData){
                    if(curr_top_y==index){
                        //删除后强制渲染
                        this.excelData.splice(index, 1);
                    }
                }
            }else{
                art.dialog.alert('请选择要操作的列或行!', null);
            }
        },
        // //重置操作
        // resetOperation:function () {
        //     var excelData = win.getExcelData();
        //     //复制为副本
        //     this.excelData = excelData.concat();
        //     //格式化表头
        //     this.formatTableHeader();
        // },
        //导入帮助
        helpImportExcel:function(){

        },
        //字段被修改
        changeColumn:function(){
            var is_matching = false;
            $.each(this.columnConfig,function(index,item){
                if(item.column_name==excelOperationVue.currColumnName){
                    if(item.position>=1){
                        $("#x_"+(item.position-1)).click();
                        is_matching = true;
                    }
                }
            });
            if(is_matching==false){
                //如果没匹配，则清空
                this.clickHeader(-1);
            }
        },
        //保存数据
        saveExcelData:function(){
            //删除多余数据
            this.deleteMoreData();
            //得到第一条数据的长度
            var data_length = this.excelData[0].length;
            //每次上传的最大阈值
            var max_length = 500;
            //得到总数据长度
            var sum_length = 0;
            $.each(this.excelData,function (index,item) {
                sum_length += item.length;
            });

            this.upload_data.curr_speed = 0;

            //分几次上传
            var upload_count = sum_length/max_length;
            //每次上传多少数据
            var upload_data_number = max_length/data_length;

            var upload_data_group = [];
            upload_data_group[0] = [];

            //已使用的长度
            var use_length = 0;
            //当前组索引
            var curr_group_index = 0;
            $.each(this.excelData,function(index,item){
                //当前长度
                var curr_length = upload_data_group[curr_group_index].length;
                upload_data_group[curr_group_index][curr_length] = item;
                use_length += item.length;
                //如果当前已使用长度大于最大长度，则换组
                if(use_length>max_length){
                    curr_group_index ++;
                    upload_data_group[curr_group_index] = [];
                    use_length=0;
                }
            });

            // 弹出新窗口
            this.progress_win = art.dialog({
                lock: true,
                opacity: 0.1,	// 透明度
                content: '上传进度：<progress id="upload_progress" value="0" max="'+this.excelData.length+'"></progress>'
            });

            this.saveDataByGroup(upload_data_group,0);
        },
        //数据分组上传
        saveDataByGroup:function(upload_data_group,curr_upload_count){

            var data = upload_data_group[curr_upload_count];

            //要保存的数据
            var saveExcelData = {
                excel_data:data,
                import_data_type:this.import_data_type,
            };
            console.log(win.getFormData());
            $.each(win.getFormData(), function(index, item){
                var name = item.name;
                var value = item.value;
                saveExcelData[name] = value;
            });

            $.ajax({
                data:saveExcelData,
                url:saveExcelDataUrl,
                dataType:'json',
                type:'POST',
                success:function(result){
                    if(result['code']==0){
                        art.dialog.alert(result['msg'], null);
                        excelOperationVue.progress_win.close();
                    }else{
                        var data = result['data'];
                        //如果当前是第一分组请求，则直接赋值
                        if(curr_upload_count==0){
                            console.log(data);
                            excelOperationVue.upload_data.result_data = data;
                        }else{
                            var result_data = excelOperationVue.upload_data.result_data;
                            //数据累加
                            result_data.error_number += data.error_number;
                            result_data.success_number += data.success_number;
                            $.each(data['insert_log_arr'],function(index,item){
                                var curr_length = result_data['insert_log_arr'].length;
                                result_data['insert_log_arr'][curr_length] = item;
                            });
                            excelOperationVue.upload_data.result_data = result_data;
                        }

                        //如果数据还未上传完，则继续调整
                        curr_upload_count++;

                        excelOperationVue.upload_data.curr_speed += upload_data_group[curr_upload_count-1].length;
                        $("#upload_progress").val(excelOperationVue.upload_data.curr_speed);

                        //如果长度分组还未上传完成，则继续上传
                        if(curr_upload_count<upload_data_group.length){
                            excelOperationVue.saveDataByGroup(upload_data_group,curr_upload_count);
                        }else{
                            setTimeout(function () {
                                //显示导入的结果
                                win.showImportResult($("#import_result").html());
                                //先关闭之前的页面
                                art.dialog.close();
                            },100);
                        }
                    }
                }, error:function(){
                    art.dialog.alert('网络请求失败,请稍后重试!', null);
                }
            })
        },
        //清除现在的td焦点
        clearTdFocus:function(){
            //清除选中的焦点
            if(this.curr_top_x!=null||this.curr_top_y!=null){
                $("[_y="+this.curr_top_y+"]").removeClass('item_selected');
                $("[_x="+this.curr_top_x+"]").removeClass('item_selected');
                this.curr_top_x = null;
                this.curr_top_y = null;
            }

            //之前失去焦点
            $("#"+this.curr_y+'_'+this.curr_x).removeClass('item_active');
            $("#y_"+this.curr_y).removeClass('index_active');
            $("#y_"+this.curr_y).removeClass('index_active');
            $("#x_"+this.curr_x).removeClass('index_active');
        },
        //显示列
        showColumn:function(){
            if(this.is_show_column){
                this.is_show_column = false;
            }else{
                this.is_show_column = true;
            }
        },
        //数据被修改
        dataChange:function(){
            $.ajax({
                data:this.masterConfig,
                url:updateConfigDataUrl,
                dataType:'json',
                type:'POST',
                success:function(result){
                    if(result['code']==0){
                        art.dialog.alert(result['msg'], null);
                    }
                }, error:function(){
                    art.dialog.alert('网络请求失败,请稍后重试!', null);
                }
            })
        },
        //添加一行
        addColumn:function(){
            this.add_column.is_add_column = true;
        },
        //保存一行
        saveColumn:function(index){

            //索引不存在则为新增保存
            if(index==null){
                var data = {
                    master_id:this.masterConfig['id'],
                    column_data:this.add_column.column_data
                }
            }
            //存在则为修改保存
            else{
                var data = {
                    column_data:this.columnConfig[index]
                }
                this.currColumnName = data.column_data['column_name'];
            }
            var _this = this;
            $.ajax({
                data:data,
                url:saveColumnDataUrl,
                dataType:'json',
                type:'POST',
                success:function(result){
                    if(result['code']==0){
                        art.dialog.alert(result['msg'], null);
                    }
                    //索引不存在则为新增保存
                    else if(index==null){
                        var column_data = {
                            column_name:'',
                            table_name:'',
                            is_unique:'',
                            position:''
                        };
                        _this.add_column.column_data['id'] = result.data;   //id赋值
                        //赋值
                        _this.columnConfig.push(_this.add_column.column_data);
                        //设置显示默认
                        _this.currColumnName = _this.add_column.column_data['column_name'];
                        //清空
                        _this.add_column.column_data = column_data;
                        excelOperationVue.add_column.is_add_column = false;
                    }
                }, error:function(){
                    art.dialog.alert('网络请求失败,请稍后重试!', null);
                }
            })
        },
        //删除一行
        deleteColumn:function(index){
            var id = this.columnConfig[index]['id'];
            var _this = this;
            art.dialog.confirm('您确定删除这一行？删除后不可恢复。', function() {
                $.ajax({
                    data:{id:id},
                    url:deleteColumnDataUrl,
                    dataType:'json',
                    type:'POST',
                    success:function(result){
                        if(result['code']==0){
                            art.dialog.alert(result['msg'], null);
                        }else{
                            //Vue删除
                            _this.columnConfig.splice(index,1);
                            if(_this.columnConfig.length>0){
                                _this.currColumnName = _this.columnConfig[0]['column_name'];
                            }
                        }
                    }, error:function(){
                        art.dialog.alert('网络请求失败,请稍后重试!', null);
                    }
                })
            });
        },
        //双击单元格
        dblClickItem:function(index,son_index){
            var item = $("#"+index+'_'+son_index);
            item.css('paddingLeft',0);
            var value = this.excelData[index][son_index];
            if(value==null){
                value = '';
            }
            item.html('<input class="item_input" id="itemInput" value="'+value+'" _index="'+index+'" _son_index="'+son_index+'" onblur="itemInputBlur()"/>');
            //得到焦点
            $("#itemInput").focus();
        },
        //根据配置删除多余的数据
        deleteMoreData:function(){
            var _this = this;
            this.curr_top_y = 0;
            this.curr_top_x = null;

            //删除多余行
            for(var i =0;i<(this.masterConfig['data_first_pos']-1);i++){
                _this.deleteCurrLine();
            }

            this.curr_top_y = null;
            this.curr_top_x = 0;

            // //删除多余列
            // for(var i =0;i<(this.masterConfig['column_first_pos']-1);i++){
            //     _this.deleteCurrLine();
            // }

            // $.each(this.columnConfig,function(index,item){
            //     _this.columnConfig[index]['position'] -= (_this.masterConfig['column_first_pos']-1);
            // });
        }
    },
});

//input失去焦点，则进行赋值
function itemInputBlur(){
    var index = $("#itemInput").attr('_index');
    var son_index = $("#itemInput").attr('_son_index');
    var input_val = $("#itemInput").val();
    var item = $("#"+index+'_'+son_index);
    item.html(input_val);
    item.css('paddingLeft',5);
    excelOperationVue.excelData[index][son_index] = input_val;
}