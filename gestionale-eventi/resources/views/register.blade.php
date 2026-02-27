@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-end mb-3">
        <a href="?lang=it"><img src="https://flagcdn.com/24x18/it.png" alt="ITA"></a>
        <a href="?lang=en"><img src="https://flagcdn.com/24x18/gb.png" alt="ENG"></a>
    </div>

    <h2>{{ $event->name ?? 'Event Registration' }}</h2>
    <p class="text-danger">{{ $t['required'] }} <span class="text-danger">*</span></p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('registrations.store') }}" id="registrationForm">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id }}">
        <input type="hidden" name="lang" value="{{ $lang }}">

        <!-- Nome -->
        <div class="mb-3">
            <label>{{ $t['first_name'] }} <span class="text-danger">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror">
            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Cognome -->
        <div class="mb-3">
            <label>{{ $t['last_name'] }} <span class="text-danger">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror">
            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label>{{ $t['email'] }} <span class="text-danger">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Telefono -->
        <div class="mb-3">
            <label>{{ $t['phone'] }} <span class="text-danger">*</span></label>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+39..." class="form-control @error('phone') is-invalid @enderror">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Data di nascita -->
        <div class="mb-3">
            <label>{{ $t['birth_date'] }} <span class="text-danger">*</span></label>
            <input type="date" name="birth_date" max="{{ now()->subYears(18)->format('Y-m-d') }}" value="{{ old('birth_date') }}" class="form-control @error('birth_date') is-invalid @enderror">
            @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Genere -->
        <div class="mb-3">
            <label>{{ $t['gender'] }} <span class="text-danger">*</span></label>
            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                <option value="">{{ $t['select'] }}</option>
                <option value="M" {{ old('gender')=='M'?'selected':'' }}>{{ $t['male'] }}</option>
                <option value="F" {{ old('gender')=='F'?'selected':'' }}>{{ $t['female'] }}</option>
                <option value="N" {{ old('gender')=='N'?'selected':'' }}>{{ $t['prefer_not'] }}</option>
            </select>
            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Nazionalità -->
        <div class="mb-3 position-relative">
            <label>{{ $t['nationality'] }} <span class="text-danger">*</span></label>
            <input type="text" id="nationalityInput" placeholder="{{ $t['select'] }}" class="form-control" autocomplete="off">
            <input type="hidden" name="nationality" id="nationalityHidden" value="{{ old('nationality') }}">
            <div id="nationalityList" class="list-group position-absolute w-100" style="z-index:1000;"></div>
            @error('nationality') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <!-- ESN Card -->
        <div class="mb-3">
            <label>{{ $t['esn_card'] }} <span class="text-danger">*</span></label>
            <input type="text" name="has_esn_card" value="{{ old('has_esn_card') }}" maxlength="12" class="form-control @error('has_esn_card') is-invalid @enderror">
            @error('has_esn_card') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Documento -->
        <div class="mb-3">
            <label>{{ $t['document_type'] }} <span class="text-danger">*</span></label>
            <select name="document_type" class="form-select @error('document_type') is-invalid @enderror">
                <option value="">{{ $t['select'] }}</option>
                <option value="id" {{ old('document_type')=='id'?'selected':'' }}>{{ $t['id_card'] }}</option>
                <option value="passport" {{ old('document_type')=='passport'?'selected':'' }}>{{ $t['passport'] }}</option>
                <option value="driver_license" {{ old('document_type')=='driver_license'?'selected':'' }}>{{ $t['driver_license'] }}</option>
            </select>
            @error('document_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label>{{ $t['document_number'] }} <span class="text-danger">*</span></label>
            <input type="text" name="document_number" value="{{ old('document_number') }}" class="form-control @error('document_number') is-invalid @enderror">
            @error('document_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ $t['submit'] }}</button>
    </form>
</div>

<!-- JS barra nazionalità + validazioni live -->
<script>
const nationalities = [
    @foreach(\App\Models\Registration::NATIONALITIES as $nation)
        {name:"{{ $nation['name'] }}", code:"{{ $nation['code'] }}"},
    @endforeach
];

const input = document.getElementById('nationalityInput');
const hidden = document.getElementById('nationalityHidden');
const list = document.getElementById('nationalityList');

input.addEventListener('input', function(){
    const val = this.value.toLowerCase();
    list.innerHTML = '';
    if(!val){ list.style.display='none'; return; }
    const filtered = nationalities.filter(n=>n.name.toLowerCase().startsWith(val));
    filtered.forEach(n=>{
        const div = document.createElement('div');
        div.classList.add('list-group-item','list-group-item-action','d-flex','align-items-center');
        div.innerHTML = `<img src="https://flagcdn.com/16x12/${n.code}.png" class="me-2">${n.name}`;
        div.addEventListener('click', ()=>{ input.value=n.name; hidden.value=n.name; list.style.display='none'; });
        list.appendChild(div);
    });
    list.style.display = filtered.length?'block':'none';
});

// Validazioni live base
document.querySelectorAll('input, select').forEach(el=>{
    el.addEventListener('input', ()=>{
        if(el.checkValidity()) el.style.borderColor = 'lightgreen';
        else el.style.borderColor = 'lightcoral';
    });
});

// Scroll al primo errore
window.onload = function(){
    const err = document.querySelector('.is-invalid');
    if(err) err.scrollIntoView({behavior:'smooth', block:'center'});
};
</script>
@endsection
