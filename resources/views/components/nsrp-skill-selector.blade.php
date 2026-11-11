@props([
    'name' => 'skills',
    'value' => '',
    'label' => 'Required Skills (NSRP Compliant)',
    'id' => 'nsrp_skills_' . uniqid()
])

@php
    $centurySkills = \App\Services\NsrpSkillService::get21stCenturySkills();
    $techSkills = \App\Services\NsrpSkillService::getTechnicalSkills();
@endphp

<div x-data="{
    selectedSkills: [],
    customSkill: '',
    showCustomInput: false,
    init() {
        let raw = `{{ addslashes($value) }}`;
        if (raw) {
            this.selectedSkills = raw.split(/[\r\n,]+/).map(s => s.trim()).filter(s => s.length > 0);
        }
    },
    addSkill(skill) {
        skill = (skill || '').trim();
        if (skill && !this.selectedSkills.includes(skill)) {
            this.selectedSkills.push(skill);
        }
    },
    removeSkill(index) {
        this.selectedSkills.splice(index, 1);
    },
    addCustom() {
        if (this.customSkill.trim()) {
            this.addSkill(this.customSkill.trim());
            this.customSkill = '';
            this.showCustomInput = false;
        }
    },
    get outputValue() {
        return this.selectedSkills.join('\n');
    }
}" class="space-y-2">
    <div class="flex items-center justify-between">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
            {{ $label }}
            <span class="text-[10px] text-emerald-700 font-semibold lowercase ml-1">(aligned with DOLE NSRP Form 1)</span>
        </label>
        <span class="text-[10px] text-slate-400 font-bold" x-text="selectedSkills.length + ' skills selected'"></span>
    </div>

    <!-- Hidden input bound to Alpine output for form submission -->
    <textarea :name="'{{ $name }}'" :value="outputValue" class="hidden" x-ref="skillsInput"></textarea>

    <!-- Standardized Dropdown Selection & Others Option -->
    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
        <div class="sm:col-span-8 relative">
            <select @change="if($event.target.value === '__OTHER__') { showCustomInput = true; $event.target.value = ''; } else if($event.target.value) { addSkill($event.target.value); $event.target.value = ''; }"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-900 bg-white focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="">-- Choose Standard NSRP Skill from List --</option>
                <optgroup label="21st Century Skills (DOLE NSRP Section VII)">
                    @foreach($centurySkills as $skill)
                        <option value="{{ $skill }}">{{ $skill }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Technical & Vocational Skills (DOLE NSRP Section IX)">
                    @foreach($techSkills as $skill)
                        <option value="{{ $skill }}">{{ $skill }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Custom Input">
                    <option value="__OTHER__">+ Others (Custom Skill Input)...</option>
                </optgroup>
            </select>
        </div>

        <div class="sm:col-span-4 flex items-center gap-1.5">
            <button type="button" @click="showCustomInput = !showCustomInput"
                    class="w-full py-2 px-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors flex items-center justify-center gap-1">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Others</span>
            </button>
        </div>
    </div>

    <!-- Custom Skill Input ("Others" option) -->
    <div x-show="showCustomInput" x-cloak class="p-3 bg-emerald-50/60 border border-emerald-200 rounded-xl space-y-2">
        <label class="block text-[11px] font-bold text-emerald-950 uppercase">Custom Skill (Others):</label>
        <div class="flex gap-2">
            <input type="text" x-model="customSkill" @keydown.enter.prevent="addCustom()"
                   placeholder="e.g. Forklift Operation, React Native, Heavy Equipment..."
                   class="flex-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white">
            <button type="button" @click="addCustom()"
                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition-colors">
                Add
            </button>
            <button type="button" @click="showCustomInput = false; customSkill = '';"
                    class="px-2.5 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                Cancel
            </button>
        </div>
    </div>

    <!-- Selected Skills Chips / Badges Container -->
    <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl min-h-[52px] flex flex-wrap items-center gap-1.5">
        <template x-for="(skill, index) in selectedSkills" :key="index">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-white border border-emerald-200 text-emerald-900 shadow-sm text-xs font-semibold">
                <span x-text="skill"></span>
                <button type="button" @click="removeSkill(index)"
                        class="h-4 w-4 rounded-full hover:bg-rose-100 hover:text-rose-700 text-slate-400 flex items-center justify-center font-bold text-[10px] transition-colors"
                        title="Remove skill">
                    &times;
                </button>
            </span>
        </template>
        <template x-if="selectedSkills.length === 0">
            <span class="text-xs text-slate-400 italic">No skills selected yet. Select from the NSRP dropdown above.</span>
        </template>
    </div>
</div>
