<?php
// 必填时的情况：输入框：**不能为空。 下拉框、单选框、复选框： 请选择*** ;字段下还可以 加 enum 用来替换 message中的{enum}
return  [
    'table_name' => '资源历史',// 表/栏目名称
    'fields' => [
        'resource_id' => [
            "field_name" => '{resource}',
            "message" => '{fieldName}{valMustInt}!'
        ],
    ],
    'judge_err' => [// 数据验证相关的错误

    ],
    'tishi' => [// 其它显示

    ]
];