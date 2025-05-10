using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Net.Http;
using System.Text.Json;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using PresenceApps;
using Newtonsoft.Json.Linq;
using Newtonsoft.Json;
using ZXing;
using ZXing.QrCode;
using ZXing.Windows.Compatibility;
using System.Threading.Tasks;
using System.Timers;

namespace PresenceApps
{
    public partial class Form1 : Form
    {
        int countdown = 20;
        private StringBuilder cardUID = new StringBuilder();
        private static readonly HttpClient client = new HttpClient();
        private readonly BindingSource bindingSource = new BindingSource();
        private static Int64 totalSiswa = 0;
        private static int positionPresence = 0;

        public Form1()
        {
            InitializeComponent();
            getDataAll();
            this.KeyPreview = true;
            this.KeyPress += new KeyPressEventHandler(Form_KeyPress);
            roundedPanel5.Visible = true;
            roundedPanel1.Visible = false;

            TimeZoneInfo indonesiaTimeZone = TimeZoneInfo.FindSystemTimeZoneById("SE Asia Standard Time");
            DateTime indonesiaTime = TimeZoneInfo.ConvertTime(DateTime.Now, indonesiaTimeZone);
            label1.Text = indonesiaTime.ToString("dd MMMM yyyy    HH:mm:ss");

            label1.AutoSize = false;
            label1.Dock = DockStyle.Fill;
            label1.TextAlign = ContentAlignment.MiddleCenter;

            roundedPanel1.BackColor = Color.White;
            roundedPanel1.BorderRadius = 50;
            roundedPanel2.BackColor = Color.White;
            roundedPanel2.BorderRadius = 50;
            roundedPanel3.BackColor = Color.White;
            roundedPanel3.BorderRadius = 50;
            roundedPanel4.BorderRadius = 50;
            roundedPanel4.BackColor = Color.White;
            roundedPanel5.BorderRadius = 50;
            roundedPanel5.BackColor = Color.White;

            lbl_totalSiswa.Anchor = AnchorStyles.None;
            lbl_totalSiswa.Location = new Point((roundedPanel3.Width - lbl_totalSiswa.Width) / 2, lbl_totalSiswa.Location.Y);

            lbl_totalSiswaHariIni.Anchor = AnchorStyles.None;
            lbl_totalSiswaHariIni.Location = new Point((roundedPanel2.Width - lbl_totalSiswaHariIni.Width) / 2, lbl_totalSiswaHariIni.Location.Y);

            lbl_siswaIjinSakit.Anchor = AnchorStyles.None;
            lbl_siswaIjinSakit.Location = new Point((roundedPanel4.Width - lbl_siswaIjinSakit.Width) / 2, lbl_siswaIjinSakit.Location.Y);

            dataGridView1.DataSource = bindingSource;
            dataGridView1.AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill;
        }

        private async void Form1_Load(object sender, EventArgs e)
        {
            //by @raadeveloperz
            //Instagram -> @x.dapzz
            await LoadDataAsync();
        }

        protected override bool ProcessCmdKey(ref Message msg, Keys keyData)
        {
            if (keyData == Keys.Enter && this.ActiveControl is Button button && button == button2)
            {
                return true;
            }
            this.ActiveControl = null;
            this.Focus();
            return base.ProcessCmdKey(ref msg, keyData);
        }

        private async Task LoadDataAsync()
        {
            try
            {
                string apiUrl = "http://localhost:8000/api/allAbsensi";
                HttpResponseMessage response = await client.GetAsync(apiUrl);

                if (response.IsSuccessStatusCode)
                {
                    string jsonResponse = await response.Content.ReadAsStringAsync();

                    // Deserialisasi ke ApiResponse
                    var apiResponse = System.Text.Json.JsonSerializer.Deserialize<ApiResponse>(
                        jsonResponse,
                        new JsonSerializerOptions { PropertyNameCaseInsensitive = true });

                    if (apiResponse?.Data != null)
                    {
                        Invoke((MethodInvoker)delegate
                        {
                            dataGridView1.DataSource = apiResponse.Data;
                            lbl_totalSiswaHariIni.Text = apiResponse.Count.ToString();
                            lbl_siswaIjinSakit.Text = apiResponse.TTH.ToString() ?? "0";
                            totalSiswa = apiResponse.Count;
                            dataGridView1.Columns["WaktuMasuk"].HeaderText = "Waktu Masuk";
                            dataGridView1.Columns["WaktuKeluar"].HeaderText = "Waktu Keluar";
                            dataGridView1.Columns["NamaLengkap"].HeaderText = "Nama Lengkap";
                            dataGridView1.Columns["StatusMasuk"].HeaderText = "Status Masuk";
                            dataGridView1.Columns["StatusKeluar"].HeaderText = "Status Keluar";
                            dataGridView1.Columns["AlasanDatang"].HeaderText = "Alasan Datang";
                            dataGridView1.Columns["AlasanPulang"].HeaderText = "Alasan Pulang";
                        });
                    }
                }
                else
                {
                    MessageBox.Show("Gagal mengambil data: " + response.StatusCode);
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error: " + ex.Message);
            }
        }

        private async void Form_KeyPress(object sender, KeyPressEventArgs e)
        {
            try
            {
                if (e.KeyChar == (char)Keys.Enter)
                {
                    if (cardUID.Length > 0 && (Convert.ToInt64(lbl_totalSiswaHariIni.Text) + Convert.ToInt64(lbl_siswaIjinSakit.Text)) < Convert.ToInt64(lbl_totalSiswa.Text))
                    {
                        roundedPanel5.Visible = true;
                        roundedPanel1.Visible = false;
                        string hexUID = cardUID.ToString().Trim();
                        await SendCardUID(hexUID);
                        cardUID.Clear();
                    }
                    else
                    {
                        if ((Convert.ToInt64(lbl_totalSiswaHariIni.Text) + Convert.ToInt64(lbl_siswaIjinSakit.Text)) >= Convert.ToInt64(lbl_totalSiswa.Text))
                        {
                            MessageBox.Show("Semuanya telah melakukan absensi masuk...", "Informasi", MessageBoxButtons.OK, MessageBoxIcon.Information);
                        }
                        else
                        {
                            roundedPanel5.Visible = true;
                            roundedPanel1.Visible = false;
                        }
                    }
                }
                else
                {
                    cardUID.Append(e.KeyChar);
                }
            }
            catch (System.FormatException ex)
            {
                MessageBox.Show("Terjadi kesalahan format angka: " + ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
            catch (Exception ex)
            {
                MessageBox.Show("Terjadi kesalahan: " + ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private async Task getDataAll()
        {
            try
            {
                HttpResponseMessage response = await client.GetAsync("http://127.0.0.1:8000/api/totalUsers");
                response.EnsureSuccessStatusCode();

                string responseString = await response.Content.ReadAsStringAsync();
                JObject jsonResponse = JObject.Parse(responseString);

                if (jsonResponse.ContainsKey("total") && jsonResponse["total"] != null)
                {
                    lbl_totalSiswa.Text = jsonResponse["total"].ToString();
                }
                else
                {
                    lbl_totalSiswa.Text = "0";
                }
            }
            catch (HttpRequestException httpEx)
            {
                MessageBox.Show("Gagal menghubungi server: " + httpEx.Message);
                lbl_totalSiswa.Text = "0";
            }
            catch (JsonReaderException jsonEx)
            {
                MessageBox.Show("Format JSON tidak valid: " + jsonEx.Message);
                lbl_totalSiswa.Text = "0";
            }
            catch (Exception ex)
            {
                MessageBox.Show("Terjadi kesalahan: " + ex.Message);
                lbl_totalSiswa.Text = "0";
            }
        }

        private void ShowQRCodeDialog(string url)
        {
            try
            {
                // Create a new form for the QR code
                Form qrForm = new Form
                {
                    Text = "Scan QR Code",
                    Size = new Size(350, 400),
                    FormBorderStyle = FormBorderStyle.FixedDialog,
                    StartPosition = FormStartPosition.CenterScreen,
                    MaximizeBox = false,
                    MinimizeBox = false
                };

                // Create QR code writer
                var writer = new ZXing.BarcodeWriter
                {
                    Format = BarcodeFormat.QR_CODE,
                    Options = new QrCodeEncodingOptions
                    {
                        Height = 300,
                        Width = 300,
                        Margin = 1
                    }
                };

                // Generate QR code bitmap
                var qrBitmap = writer.Write(url);

                // Create PictureBox to display QR code
                PictureBox pictureBox = new PictureBox
                {
                    Image = qrBitmap,
                    SizeMode = PictureBoxSizeMode.StretchImage,
                    Dock = DockStyle.Top,
                    Height = 300
                };

                // Create label with instructions
                Label label = new Label
                {
                    Text = "Scan QR code untuk melengkapi absensi Anda\nJendela ini akan tertutup dalam 20 detik",
                    TextAlign = ContentAlignment.MiddleCenter,
                    Dock = DockStyle.Bottom,
                    Height = 50
                };

                // Add controls to form
                qrForm.Controls.Add(pictureBox);
                qrForm.Controls.Add(label);

                // Create timer to close the form after 20 seconds
                System.Windows.Forms.Timer timer = new System.Windows.Forms.Timer
                {
                    Interval = 1000, // 20 seconds
                    Enabled = true
                };

                timer.Tick += (sender, e) => {
                    countdown--;
                    label.Text = $"Scan QR code untuk melengkapi absensi Anda\nTutup dalam {countdown} detik...";

                    if (countdown <= 0)
                    {
                        timer.Stop();
                        qrForm.Close();
                    }
                    //timer.Stop();
                    //qrForm.Close();
                };

                // Show the form as non-modal dialog
                qrForm.Show();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error saat membuat QR Code: " + ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private async Task SendCardUID(string uid)
        {
            try
            {
                var data = new { id = uid };
                string json = Newtonsoft.Json.JsonConvert.SerializeObject(data);
                var content = new StringContent(json, Encoding.UTF8, "application/json");

                HttpResponseMessage response = await client.PostAsync("http://127.0.0.1:8000/api/validate-acc", content);
                string responseString = await response.Content.ReadAsStringAsync();

                JObject jsonResponse = JObject.Parse(responseString);

                if (jsonResponse["status"].ToString() == "success")
                {
                    var data2 = new
                    {
                        nis = jsonResponse["data"]["nis"].ToString(),
                        id = uid
                    };
                    string json2 = Newtonsoft.Json.JsonConvert.SerializeObject(data2);
                    var content2 = new StringContent(json2, Encoding.UTF8, "application/json");

                    // Determine which API endpoint to call based on positionPresence
                    string apiEndpoint = positionPresence == 0 ?
                        "http://127.0.0.1:8000/api/absensiMasuk" :
                        "http://127.0.0.1:8000/api/absensiKeluar";

                    HttpResponseMessage response2 = await client.PostAsync(apiEndpoint, content2);
                    string responseString2 = await response2.Content.ReadAsStringAsync();
                    JObject jsonResponse2 = JObject.Parse(responseString2);

                    string status = jsonResponse2["status"].ToString();

                    if (status == "success")
                    {
                        await LoadDataAsync();
                        lbl_nama.Text = jsonResponse["data"]["name"].ToString();
                        lbl_kelas.Text = jsonResponse["data"]["kelas"].ToString() + " " + jsonResponse["data"]["jurusan"].ToString() + " " + jsonResponse["data"]["angka_kelas"].ToString();
                        lbl_nis.Text = jsonResponse["data"]["nis"].ToString();

                        string imageUrl = jsonResponse["profile"]?.ToString();

                        if (!string.IsNullOrEmpty(imageUrl))
                        {
                            await LoadImageFromUrl(imageUrl);
                        }
                        else
                        {
                            pictureBox1.Image = Properties.Resources.ResourceManager.GetObject("default_image") as Image;
                        }

                        roundedPanel5.Visible = false;
                        roundedPanel1.Visible = true;
                    }
                    else if (status == "failed")
                    {
                        roundedPanel5.Visible = true;
                        roundedPanel1.Visible = false;
                        MessageBox.Show(jsonResponse2["message"].ToString(), "Informasi", MessageBoxButtons.OK, MessageBoxIcon.Information);
                    }
                    // Special handling for "libur" status - only show message, no QR code
                    else if (status == "libur")
                    {
                        roundedPanel5.Visible = true;
                        roundedPanel1.Visible = false;
                        MessageBox.Show(jsonResponse2["message"].ToString(), "Informasi", MessageBoxButtons.OK, MessageBoxIcon.Information);
                    }
                    // Handle other redirect URL statuses (late, early, non_productive)
                    else if (status == "late" || status == "early" || status == "non_productive")
                    {
                        // Check if redirect_url exists in the response
                        if (jsonResponse2["redirect_url"] != null)
                        {
                            string redirectUrl = jsonResponse2["redirect_url"].ToString();
                            ShowQRCodeDialog(redirectUrl);
                        }

                        roundedPanel5.Visible = true;
                        roundedPanel1.Visible = false;
                    }
                }
                else
                {
                    roundedPanel5.Visible = true;
                    roundedPanel1.Visible = false;
                    MessageBox.Show(jsonResponse["message"].ToString(), "Informasi", MessageBoxButtons.OK, MessageBoxIcon.Information);
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show("Error: " + ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private async Task LoadImageFromUrl(string url)
        {
            try
            {
                using (HttpClient client = new HttpClient())
                {
                    var response = await client.GetAsync(url);
                    response.EnsureSuccessStatusCode();

                    using (var stream = await response.Content.ReadAsStreamAsync())
                    {
                        pictureBox2.Image = Image.FromStream(stream);
                    }
                }
            }
            catch
            {
                pictureBox2.Image = Properties.Resources.ResourceManager.GetObject("default_image") as Image;
            }
        }

        private void timer1_Tick(object sender, EventArgs e)
        {
            TimeZoneInfo indonesiaTimeZone = TimeZoneInfo.FindSystemTimeZoneById("SE Asia Standard Time");
            DateTime indonesiaTime = TimeZoneInfo.ConvertTime(DateTime.Now, indonesiaTimeZone);
            label1.Text = indonesiaTime.ToString("dd MMMM yyyy    HH:mm:ss");
        }

        private async void timer2_Tick(object sender, EventArgs e)
        {
            await LoadDataAsync();
        }

        private void button2_Click(object sender, EventArgs e)
        {
            if (positionPresence == 0)
            {
                button2.Text = "Absensi Masuk";
                positionPresence = 1;
                button2.BackColor = Color.Green;
                lbl_modeAbsensi.Text = "Absensi Keluar";
            }
            else
            {
                button2.Text = "Absensi Keluar";
                positionPresence = 0;
                button2.BackColor = Color.Red;
                lbl_modeAbsensi.Text = "Absensi Masuk";
            }
        }
    }
}