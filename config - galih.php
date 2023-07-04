<?php
return [
    'bot_token'=>'6048965387:AAFuIs05Diio7wsgrdBx4Jngvu7qd-Ejn9I',
    'botuname'=>'gj_subsbot',
    'webhook'=>'/gj_subsbot/webhook_9x56fe073bc03c84eaecabfc6f43a78y.php',
    'bot_admins'=>[
        '227024160',
    ],
    'admin_chat_id'=>'-1001885407235',
    'log_chat_id'=>'-1001885407235',
	's4s_channel'=>'@gj_subs',
    'force_subs'=>[
        '@gj_subs',
    ],
	'detik_check_channeluname' => 300, //tiap job jalan, cek (tiap .. detik) apakah username channel berubah
	'detik_check_subs_user' => 5, //tiap user act, cek subscribnya jika sudah melewati .. detik
	'detik_check_subs_all_users' => 10, //job, cek semua subscribe user jika sudah melewati .. detik
	'max_check_all_user_subs'=>2, //jumlah maksimal user dalam sekali job (kalau sudah max, user selanjutnya dicek di job berikutnya)
    'msgcmd'=>[
        '/start'=>['sendMessage',
			[
				'text'=>"Dapatkan poin dari @gj_subs, lalu gunakan itu untuk mendapatkan subscriber!\n"
				    ."/my_sbp - lihat SuBscribe Point\n"
					."/my_channels – lihat daftar channel\n"
					."/add_channel – tambah channel\n"
					."/help – lihat cara menggunakan\n",
			]
		],
        '/help'=>['sendMessage',
			[
				'text'=>"SBP = Subscribe Poin\n".
					"• Jika subscribe, + 1 SBP (gunakan link [Sudah Join] di channel @gj_subs).\n".
					"• Jika unsubscribe, bisa kena banned (lihat /penalty)\n".
					"• Ajak pengguna baru, + 1 SBP (gunakan /invite)\n\n".
					"Untuk menambahkan channel:\n".
					"• Minimal punya 1 SBP\n".
					"• Tambahkan bot menjadi admin channel\n".
					"• Channel harus public\n".
					"• user harus admin channel\n".
					"• 1 channel untuk 1 user dalam 1 waktu\n".
					"• 1 user bisa banyak channel\n".
					"• Gunakan command /add_channel\n".
					"• Masukkan username channel diawali tanda @\n".
					"• Channel akan diposting di @gj_subs\n".
					"• Jika ada pengguna yg join melalui postingan tsb, SBP akan berkurang 1\n".
					"• Jika SBP habis, postingan dihapus".
					"• Dilarang mengeluarkan bot dari channel atau mengganti username channel",
			]
		],
        '/penalty'=>['sendMessage',
			[
				'text'=>"Jika Anda unsubscribe:\n".
					"• SBP berkurang 1 (bisa negatif)\n".
					"Jika yang diunsubscribe bukan channel yang kena banned:\n".
					"• User anda di-banned (tidak bisa dapat SBP lagi)\n".
					"• Channel-channel yang pernah Anda tambahkan di-banned (dihapus dan tidak bisa ditambahkan lagi oleh siapapun)\n".
					"• Akan ada informasi agar channel-channel Anda diunsubscribe juga oleh pengguna lain\n\n".
					"Banned di atas juga berlaku jika:\n".
					"• Anda mengeluarkan bot dari channel\n".
					"• Anda mengganti username channel\n".
					"• Anda melakukan hal yang tidak semestinya\n"
					,
			]
		],
        '/donate'=>['sendMessage',['text'=>'Yuk, donasi buat @galihjkdev :D',]],
    ],
];