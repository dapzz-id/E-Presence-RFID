namespace PresenceApps
{
    partial class Form1
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.components = new System.ComponentModel.Container();
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(Form1));
            this.label1 = new System.Windows.Forms.Label();
            this.panel1 = new System.Windows.Forms.Panel();
            this.timer1 = new System.Windows.Forms.Timer(this.components);
            this.dataGridView1 = new System.Windows.Forms.DataGridView();
            this.button2 = new System.Windows.Forms.Button();
            this.timer2 = new System.Windows.Forms.Timer(this.components);
            this.label5 = new System.Windows.Forms.Label();
            this.lbl_modeAbsensi = new System.Windows.Forms.Label();
            this.roundedPanel4 = new PresenceApps.RoundedPanel();
            this.lbl_siswaIjinSakit = new System.Windows.Forms.Label();
            this.label4 = new System.Windows.Forms.Label();
            this.roundedPanel3 = new PresenceApps.RoundedPanel();
            this.lbl_totalSiswa = new System.Windows.Forms.Label();
            this.label3 = new System.Windows.Forms.Label();
            this.roundedPanel2 = new PresenceApps.RoundedPanel();
            this.lbl_totalSiswaHariIni = new System.Windows.Forms.Label();
            this.label15 = new System.Windows.Forms.Label();
            this.roundedPanel5 = new PresenceApps.RoundedPanel();
            this.label2 = new System.Windows.Forms.Label();
            this.pictureBox1 = new System.Windows.Forms.PictureBox();
            this.label14 = new System.Windows.Forms.Label();
            this.roundedPanel1 = new PresenceApps.RoundedPanel();
            this.pictureBox2 = new System.Windows.Forms.PictureBox();
            this.lbl_nis = new System.Windows.Forms.Label();
            this.lbl_kelas = new System.Windows.Forms.Label();
            this.lbl_nama = new System.Windows.Forms.Label();
            this.label9 = new System.Windows.Forms.Label();
            this.label10 = new System.Windows.Forms.Label();
            this.label11 = new System.Windows.Forms.Label();
            this.label12 = new System.Windows.Forms.Label();
            this.panel1.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.dataGridView1)).BeginInit();
            this.roundedPanel4.SuspendLayout();
            this.roundedPanel3.SuspendLayout();
            this.roundedPanel2.SuspendLayout();
            this.roundedPanel5.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).BeginInit();
            this.roundedPanel1.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox2)).BeginInit();
            this.SuspendLayout();
            // 
            // label1
            // 
            this.label1.Anchor = ((System.Windows.Forms.AnchorStyles)((((System.Windows.Forms.AnchorStyles.Top | System.Windows.Forms.AnchorStyles.Bottom) 
            | System.Windows.Forms.AnchorStyles.Left) 
            | System.Windows.Forms.AnchorStyles.Right)));
            this.label1.Font = new System.Drawing.Font("Franklin Gothic Demi", 13.8F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(350, 29);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(1205, 64);
            this.label1.TabIndex = 0;
            this.label1.Text = "12:11:39";
            this.label1.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // panel1
            // 
            this.panel1.Controls.Add(this.label1);
            this.panel1.Dock = System.Windows.Forms.DockStyle.Top;
            this.panel1.Location = new System.Drawing.Point(0, 0);
            this.panel1.Name = "panel1";
            this.panel1.Size = new System.Drawing.Size(1902, 95);
            this.panel1.TabIndex = 1;
            // 
            // timer1
            // 
            this.timer1.Enabled = true;
            this.timer1.Tick += new System.EventHandler(this.timer1_Tick);
            // 
            // dataGridView1
            // 
            this.dataGridView1.AllowUserToAddRows = false;
            this.dataGridView1.AllowUserToDeleteRows = false;
            this.dataGridView1.AllowUserToOrderColumns = true;
            this.dataGridView1.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize;
            this.dataGridView1.Location = new System.Drawing.Point(156, 479);
            this.dataGridView1.Name = "dataGridView1";
            this.dataGridView1.ReadOnly = true;
            this.dataGridView1.RowHeadersWidth = 51;
            this.dataGridView1.RowTemplate.Height = 24;
            this.dataGridView1.Size = new System.Drawing.Size(1683, 400);
            this.dataGridView1.TabIndex = 11;
            // 
            // button2
            // 
            this.button2.BackColor = System.Drawing.Color.Red;
            this.button2.Cursor = System.Windows.Forms.Cursors.Hand;
            this.button2.Font = new System.Drawing.Font("Verdana", 7.8F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(238)));
            this.button2.ForeColor = System.Drawing.Color.White;
            this.button2.Location = new System.Drawing.Point(1664, 898);
            this.button2.Name = "button2";
            this.button2.Padding = new System.Windows.Forms.Padding(8, 2, 8, 2);
            this.button2.Size = new System.Drawing.Size(175, 54);
            this.button2.TabIndex = 13;
            this.button2.Text = "Absensi Keluar";
            this.button2.UseVisualStyleBackColor = false;
            this.button2.Click += new System.EventHandler(this.button2_Click);
            // 
            // timer2
            // 
            this.timer2.Enabled = true;
            this.timer2.Interval = 25000;
            this.timer2.Tick += new System.EventHandler(this.timer2_Tick);
            // 
            // label5
            // 
            this.label5.AutoSize = true;
            this.label5.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label5.Location = new System.Drawing.Point(1247, 909);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(73, 25);
            this.label5.TabIndex = 14;
            this.label5.Text = "Mode:";
            // 
            // lbl_modeAbsensi
            // 
            this.lbl_modeAbsensi.AutoSize = true;
            this.lbl_modeAbsensi.Font = new System.Drawing.Font("Microsoft Sans Serif", 12F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.lbl_modeAbsensi.Location = new System.Drawing.Point(1321, 910);
            this.lbl_modeAbsensi.Name = "lbl_modeAbsensi";
            this.lbl_modeAbsensi.Size = new System.Drawing.Size(160, 25);
            this.lbl_modeAbsensi.TabIndex = 15;
            this.lbl_modeAbsensi.Text = "Absensi Masuk";
            this.lbl_modeAbsensi.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // roundedPanel4
            // 
            this.roundedPanel4.BorderColor = System.Drawing.Color.Black;
            this.roundedPanel4.BorderRadius = 30;
            this.roundedPanel4.BorderThickness = 3;
            this.roundedPanel4.Controls.Add(this.lbl_siswaIjinSakit);
            this.roundedPanel4.Controls.Add(this.label4);
            this.roundedPanel4.Location = new System.Drawing.Point(1138, 101);
            this.roundedPanel4.Name = "roundedPanel4";
            this.roundedPanel4.Size = new System.Drawing.Size(343, 343);
            this.roundedPanel4.TabIndex = 10;
            // 
            // lbl_siswaIjinSakit
            // 
            this.lbl_siswaIjinSakit.AutoSize = true;
            this.lbl_siswaIjinSakit.Font = new System.Drawing.Font("Microsoft Sans Serif", 28.2F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.lbl_siswaIjinSakit.Location = new System.Drawing.Point(133, 144);
            this.lbl_siswaIjinSakit.Name = "lbl_siswaIjinSakit";
            this.lbl_siswaIjinSakit.Size = new System.Drawing.Size(50, 54);
            this.lbl_siswaIjinSakit.TabIndex = 6;
            this.lbl_siswaIjinSakit.Text = "0";
            this.lbl_siswaIjinSakit.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label4
            // 
            this.label4.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Left | System.Windows.Forms.AnchorStyles.Right)));
            this.label4.AutoSize = true;
            this.label4.Font = new System.Drawing.Font("Trebuchet MS", 10.2F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.ImageAlign = System.Drawing.ContentAlignment.MiddleRight;
            this.label4.Location = new System.Drawing.Point(63, 23);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(200, 23);
            this.label4.TabIndex = 0;
            this.label4.Text = "TOTAL SISWA IZIN/SAKIT";
            this.label4.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // roundedPanel3
            // 
            this.roundedPanel3.BorderColor = System.Drawing.Color.Black;
            this.roundedPanel3.BorderRadius = 30;
            this.roundedPanel3.BorderThickness = 3;
            this.roundedPanel3.Controls.Add(this.lbl_totalSiswa);
            this.roundedPanel3.Controls.Add(this.label3);
            this.roundedPanel3.Location = new System.Drawing.Point(1496, 101);
            this.roundedPanel3.Name = "roundedPanel3";
            this.roundedPanel3.Size = new System.Drawing.Size(343, 343);
            this.roundedPanel3.TabIndex = 10;
            // 
            // lbl_totalSiswa
            // 
            this.lbl_totalSiswa.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Left | System.Windows.Forms.AnchorStyles.Right)));
            this.lbl_totalSiswa.Font = new System.Drawing.Font("Microsoft Sans Serif", 28.2F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.lbl_totalSiswa.Location = new System.Drawing.Point(93, 144);
            this.lbl_totalSiswa.Name = "lbl_totalSiswa";
            this.lbl_totalSiswa.Size = new System.Drawing.Size(130, 54);
            this.lbl_totalSiswa.TabIndex = 6;
            this.lbl_totalSiswa.Text = "0";
            this.lbl_totalSiswa.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // label3
            // 
            this.label3.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Left | System.Windows.Forms.AnchorStyles.Right)));
            this.label3.AutoSize = true;
            this.label3.Font = new System.Drawing.Font("Trebuchet MS", 10.2F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label3.ImageAlign = System.Drawing.ContentAlignment.MiddleRight;
            this.label3.Location = new System.Drawing.Point(50, 23);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(233, 23);
            this.label3.TabIndex = 0;
            this.label3.Text = "TOTAL SISWA KESELURUHAN";
            this.label3.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // roundedPanel2
            // 
            this.roundedPanel2.BorderColor = System.Drawing.Color.Black;
            this.roundedPanel2.BorderRadius = 30;
            this.roundedPanel2.BorderThickness = 3;
            this.roundedPanel2.Controls.Add(this.lbl_totalSiswaHariIni);
            this.roundedPanel2.Controls.Add(this.label15);
            this.roundedPanel2.Location = new System.Drawing.Point(780, 101);
            this.roundedPanel2.Name = "roundedPanel2";
            this.roundedPanel2.Size = new System.Drawing.Size(343, 343);
            this.roundedPanel2.TabIndex = 9;
            // 
            // lbl_totalSiswaHariIni
            // 
            this.lbl_totalSiswaHariIni.AutoSize = true;
            this.lbl_totalSiswaHariIni.Font = new System.Drawing.Font("Microsoft Sans Serif", 28.2F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.lbl_totalSiswaHariIni.Location = new System.Drawing.Point(133, 144);
            this.lbl_totalSiswaHariIni.Name = "lbl_totalSiswaHariIni";
            this.lbl_totalSiswaHariIni.Size = new System.Drawing.Size(50, 54);
            this.lbl_totalSiswaHariIni.TabIndex = 6;
            this.lbl_totalSiswaHariIni.Text = "0";
            this.lbl_totalSiswaHariIni.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label15
            // 
            this.label15.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Left | System.Windows.Forms.AnchorStyles.Right)));
            this.label15.AutoSize = true;
            this.label15.Font = new System.Drawing.Font("Trebuchet MS", 10.2F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label15.ImageAlign = System.Drawing.ContentAlignment.MiddleRight;
            this.label15.Location = new System.Drawing.Point(76, 23);
            this.label15.Name = "label15";
            this.label15.Size = new System.Drawing.Size(180, 23);
            this.label15.TabIndex = 0;
            this.label15.Text = "TOTAL SISWA HARI INI";
            this.label15.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // roundedPanel5
            // 
            this.roundedPanel5.BorderColor = System.Drawing.Color.Black;
            this.roundedPanel5.BorderRadius = 30;
            this.roundedPanel5.BorderThickness = 3;
            this.roundedPanel5.Controls.Add(this.label2);
            this.roundedPanel5.Controls.Add(this.pictureBox1);
            this.roundedPanel5.Controls.Add(this.label14);
            this.roundedPanel5.Location = new System.Drawing.Point(156, 101);
            this.roundedPanel5.Name = "roundedPanel5";
            this.roundedPanel5.Size = new System.Drawing.Size(549, 343);
            this.roundedPanel5.TabIndex = 9;
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Font = new System.Drawing.Font("Microsoft Sans Serif", 10.2F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(53, 242);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(396, 20);
            this.label2.TabIndex = 2;
            this.label2.Text = "TEMPELKAN KARTU PELAJAR KE NFC RFID";
            // 
            // pictureBox1
            // 
            this.pictureBox1.Image = ((System.Drawing.Image)(resources.GetObject("pictureBox1.Image")));
            this.pictureBox1.Location = new System.Drawing.Point(200, 117);
            this.pictureBox1.Name = "pictureBox1";
            this.pictureBox1.Size = new System.Drawing.Size(148, 88);
            this.pictureBox1.SizeMode = System.Windows.Forms.PictureBoxSizeMode.Zoom;
            this.pictureBox1.TabIndex = 1;
            this.pictureBox1.TabStop = false;
            // 
            // label14
            // 
            this.label14.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Left | System.Windows.Forms.AnchorStyles.Right)));
            this.label14.AutoSize = true;
            this.label14.Font = new System.Drawing.Font("Trebuchet MS", 10.2F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label14.ImageAlign = System.Drawing.ContentAlignment.MiddleRight;
            this.label14.Location = new System.Drawing.Point(148, 23);
            this.label14.Name = "label14";
            this.label14.Size = new System.Drawing.Size(230, 23);
            this.label14.TabIndex = 0;
            this.label14.Text = "RIWAYAT ABSENSI TERAKHIR";
            this.label14.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // roundedPanel1
            // 
            this.roundedPanel1.BorderColor = System.Drawing.Color.Black;
            this.roundedPanel1.BorderRadius = 30;
            this.roundedPanel1.BorderThickness = 3;
            this.roundedPanel1.Controls.Add(this.pictureBox2);
            this.roundedPanel1.Controls.Add(this.lbl_nis);
            this.roundedPanel1.Controls.Add(this.lbl_kelas);
            this.roundedPanel1.Controls.Add(this.lbl_nama);
            this.roundedPanel1.Controls.Add(this.label9);
            this.roundedPanel1.Controls.Add(this.label10);
            this.roundedPanel1.Controls.Add(this.label11);
            this.roundedPanel1.Controls.Add(this.label12);
            this.roundedPanel1.Location = new System.Drawing.Point(156, 101);
            this.roundedPanel1.Name = "roundedPanel1";
            this.roundedPanel1.Size = new System.Drawing.Size(549, 343);
            this.roundedPanel1.TabIndex = 8;
            // 
            // pictureBox2
            // 
            this.pictureBox2.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Left | System.Windows.Forms.AnchorStyles.Right)));
            this.pictureBox2.Image = ((System.Drawing.Image)(resources.GetObject("pictureBox2.Image")));
            this.pictureBox2.Location = new System.Drawing.Point(209, 55);
            this.pictureBox2.Name = "pictureBox2";
            this.pictureBox2.Size = new System.Drawing.Size(130, 175);
            this.pictureBox2.SizeMode = System.Windows.Forms.PictureBoxSizeMode.Zoom;
            this.pictureBox2.TabIndex = 7;
            this.pictureBox2.TabStop = false;
            // 
            // lbl_nis
            // 
            this.lbl_nis.AutoSize = true;
            this.lbl_nis.Font = new System.Drawing.Font("Microsoft Sans Serif", 9F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.lbl_nis.Location = new System.Drawing.Point(93, 245);
            this.lbl_nis.Name = "lbl_nis";
            this.lbl_nis.Size = new System.Drawing.Size(89, 18);
            this.lbl_nis.TabIndex = 6;
            this.lbl_nis.Text = "232410012";
            this.lbl_nis.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // lbl_kelas
            // 
            this.lbl_kelas.AutoSize = true;
            this.lbl_kelas.Font = new System.Drawing.Font("Microsoft Sans Serif", 9F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.lbl_kelas.Location = new System.Drawing.Point(93, 290);
            this.lbl_kelas.Name = "lbl_kelas";
            this.lbl_kelas.Size = new System.Drawing.Size(74, 18);
            this.lbl_kelas.TabIndex = 5;
            this.lbl_kelas.Text = "XI RPL 2";
            this.lbl_kelas.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // lbl_nama
            // 
            this.lbl_nama.AutoSize = true;
            this.lbl_nama.Font = new System.Drawing.Font("Microsoft Sans Serif", 9F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.lbl_nama.Location = new System.Drawing.Point(93, 267);
            this.lbl_nama.Name = "lbl_nama";
            this.lbl_nama.Size = new System.Drawing.Size(300, 18);
            this.lbl_nama.TabIndex = 4;
            this.lbl_nama.Text = "DIRTANDRA PUTRA TAUFIQ AL-RAFI\'I";
            this.lbl_nama.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // label9
            // 
            this.label9.AutoSize = true;
            this.label9.Location = new System.Drawing.Point(35, 292);
            this.label9.Name = "label9";
            this.label9.Size = new System.Drawing.Size(52, 16);
            this.label9.TabIndex = 3;
            this.label9.Text = "KELAS:";
            // 
            // label10
            // 
            this.label10.AutoSize = true;
            this.label10.Location = new System.Drawing.Point(35, 268);
            this.label10.Name = "label10";
            this.label10.Size = new System.Drawing.Size(49, 16);
            this.label10.TabIndex = 2;
            this.label10.Text = "NAMA:";
            // 
            // label11
            // 
            this.label11.AutoSize = true;
            this.label11.Location = new System.Drawing.Point(35, 246);
            this.label11.Name = "label11";
            this.label11.Size = new System.Drawing.Size(35, 16);
            this.label11.TabIndex = 1;
            this.label11.Text = "NIS :";
            // 
            // label12
            // 
            this.label12.Anchor = ((System.Windows.Forms.AnchorStyles)((System.Windows.Forms.AnchorStyles.Left | System.Windows.Forms.AnchorStyles.Right)));
            this.label12.AutoSize = true;
            this.label12.Font = new System.Drawing.Font("Trebuchet MS", 10.2F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label12.ImageAlign = System.Drawing.ContentAlignment.MiddleRight;
            this.label12.Location = new System.Drawing.Point(148, 23);
            this.label12.Name = "label12";
            this.label12.Size = new System.Drawing.Size(230, 23);
            this.label12.TabIndex = 0;
            this.label12.Text = "RIWAYAT ABSENSI TERAKHIR";
            this.label12.TextAlign = System.Drawing.ContentAlignment.MiddleLeft;
            // 
            // Form1
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(8F, 16F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.AutoScroll = true;
            this.BackColor = System.Drawing.Color.Honeydew;
            this.ClientSize = new System.Drawing.Size(1902, 1033);
            this.Controls.Add(this.lbl_modeAbsensi);
            this.Controls.Add(this.label5);
            this.Controls.Add(this.button2);
            this.Controls.Add(this.dataGridView1);
            this.Controls.Add(this.roundedPanel4);
            this.Controls.Add(this.roundedPanel3);
            this.Controls.Add(this.roundedPanel2);
            this.Controls.Add(this.panel1);
            this.Controls.Add(this.roundedPanel5);
            this.Controls.Add(this.roundedPanel1);
            this.Name = "Form1";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "E-Presence Apps";
            this.WindowState = System.Windows.Forms.FormWindowState.Maximized;
            this.Load += new System.EventHandler(this.Form1_Load);
            this.panel1.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.dataGridView1)).EndInit();
            this.roundedPanel4.ResumeLayout(false);
            this.roundedPanel4.PerformLayout();
            this.roundedPanel3.ResumeLayout(false);
            this.roundedPanel3.PerformLayout();
            this.roundedPanel2.ResumeLayout(false);
            this.roundedPanel2.PerformLayout();
            this.roundedPanel5.ResumeLayout(false);
            this.roundedPanel5.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).EndInit();
            this.roundedPanel1.ResumeLayout(false);
            this.roundedPanel1.PerformLayout();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox2)).EndInit();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.Panel panel1;
        private System.Windows.Forms.Timer timer1;
        private RoundedPanel roundedPanel1;
        private System.Windows.Forms.PictureBox pictureBox2;
        private System.Windows.Forms.Label lbl_nis;
        private System.Windows.Forms.Label lbl_kelas;
        private System.Windows.Forms.Label lbl_nama;
        private System.Windows.Forms.Label label9;
        private System.Windows.Forms.Label label10;
        private System.Windows.Forms.Label label11;
        private System.Windows.Forms.Label label12;
        private RoundedPanel roundedPanel2;
        private System.Windows.Forms.Label lbl_totalSiswaHariIni;
        private System.Windows.Forms.Label label15;
        private RoundedPanel roundedPanel3;
        private System.Windows.Forms.Label lbl_totalSiswa;
        private System.Windows.Forms.Label label3;
        private RoundedPanel roundedPanel4;
        private System.Windows.Forms.Label lbl_siswaIjinSakit;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.DataGridView dataGridView1;
        private RoundedPanel roundedPanel5;
        private System.Windows.Forms.PictureBox pictureBox1;
        private System.Windows.Forms.Label label14;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.Button button2;
        private System.Windows.Forms.Timer timer2;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.Label lbl_modeAbsensi;
    }
}

