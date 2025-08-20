<?php

namespace App\Livewire;

use App\Models\GeomappingUser;
use App\Models\Region;
use App\Models\Province;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use PHPMailer\PHPMailer\PHPMailer;

class InvestmentRegistration extends Component
{
    use WithFileUploads;

    public $image;
    public $firstname, $middlename, $lastname;
    public $affiliation, $designation;
    public $gender, $phone, $email;
    public $vulnerability, $food_restriction;
    public $region;
    public $province;

    public $regions = [];
    public $provinces = [];

    protected $rules = [
        'image'            => 'required|image|max:2048',
        'firstname'        => 'required|min:2',
        'middlename'       => 'nullable|min:2',
        'lastname'         => 'required|min:2',
        'affiliation'      => 'required|string',
        'designation'      => 'required|string',
        'gender'           => 'required|in:Male,Female,Other',
        'phone'            => 'required|numeric|digits:11',
        'email'            => 'required|email|unique:geomapping_users,email',
        'region'           => 'required',
        'province'         => 'required|string',
        'vulnerability'    => 'required|string',
        'food_restriction' => 'required|string',
    ];

    public function mount()
    {
        $this->regions = Region::all();
        $this->provinces = collect();
    }

    public function updatedRegion($value)
    {
        $provs = Province::where('REGION_ID', $value)->orderBy('PROVINCE')->get();
        $this->provinces = $provs;
        $this->province = null;
    }


    public function register()
    {
        $this->validate();

        $filename = time() . '.' . $this->image->getClientOriginalExtension();
        $this->image->storeAs('investmentforum2025', $filename, 'public');
        $imagePath = 'storage/investmentforum2025/' . $filename;

        $loginCode = strtoupper(Str::random(8));

        GeomappingUser::create([
            'name' => $this->firstname . ' ' . $this->middlename . ' ' . $this->lastname,
            'firstname'        => $this->firstname,
            'middlename'       => $this->middlename,
            'lastname'         => $this->lastname,
            'affiliation'      => $this->affiliation,
            'designation'      => $this->designation,
            'gender'           => $this->gender,
            'phone'            => $this->phone,
            'email'            => $this->email,
            'vulnerability'    => $this->vulnerability,
            'food_restriction' => $this->food_restriction,
            'region_id'           => $this->region,
            'province_id'         => $this->province,
            'login_code'       => $loginCode,
            'image'            => $imagePath,
        ]);

        session()->flash('message', "✅ Registration successful! Your login code is: {$loginCode}");

        // return redirect()->to('/dashboard');
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'prdponline.ggu@gmail.com'; // your Gmail
            $mail->Password   = 'sidx daut wjse asas';       // app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('prdponline.ggu@gmail.com', 'PRDP Investment Forum');
            $mail->addAddress($this->email, $this->firstname . ' ' . $this->lastname);

            $mail->isHTML(true);
            $mail->Subject = 'PRDP Investment Forum Registration Confirmation';
            $mail->Body    = "<p>Thank you for registering, {$this->firstname}!</p><p>Your login code is: <strong>{$loginCode}</strong></p>";

            // Attach image
            $mail->addAttachment(public_path('storage/investmentforum2025/' . $filename), 'Photo.jpg');

            $mail->send();
        } catch (Exception $e) {
            logger()->error("Email sending failed: {$mail->ErrorInfo}");
        }

        $this->reset();
        LivewireAlert::title('Success!')
            ->text('You have been successfully registered.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();
    }

    public function resetForm()
    {
        $this->reset([
            'image',
            'firstname',
            'middlename',
            'lastname',
            'affiliation',
            'designation',
            'gender',
            'phone',
            'email',
            'region',
            'province',
            'vulnerability',
            'food_restriction',
        ]);

        $this->provinces = collect(); // Optional: clear provinces if needed
    }

    public function render()
    {
        return view('livewire.geomapping.iplan.investment-registration', [
            'regions' => $this->regions,
            'provinces' => $this->provinces,
        ])->layout('components.layouts.investmentForum2025.app');
    }
}
