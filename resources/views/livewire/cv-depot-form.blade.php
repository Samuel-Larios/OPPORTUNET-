@php
    $user = auth()->user();
@endphp

<div class="cv-form-shell reveal">
    <div class="cv-form-copy">
        <span class="section-label">{{ __('cv_services.form.label') }}</span>
        <h2 class="section-title">{{ __('cv_services.form.title') }}</h2>
        <p class="section-sub">{{ __('cv_services.form.subtitle') }}</p>

        <div class="cv-upload-note">
            <strong>{{ __('cv_services.form.pdf_title') }}</strong>
            <span>{{ __('cv_services.form.pdf_note') }}</span>
        </div>

        @auth
            <div class="cv-upload-note cv-upload-note-soft">
                <strong>{{ __('cv_services.form.space_title') }}</strong>
                <span>{{ __('cv_services.form.space_note') }}</span>
            </div>
        @endauth
    </div>

    <div>
        @if (session('cv_success'))
            <div class="home-alert success reveal">{{ session('cv_success') }}</div>
        @endif

        @guest
            <div class="cv-form-card cv-auth-card">
                <strong>{{ __('cv_services.form.auth_title') }}</strong>
                <p>{{ __('cv_services.form.auth_text') }}</p>
                <div class="contact-form-actions">
                    <a href="{{ route('login') }}" class="solid-submit">{{ __('admin.auth.login_submit') }}</a>
                    <a href="{{ route('register.user') }}" class="ghost-submit">{{ __('admin.auth.create_simple_user_account') }}</a>
                </div>
            </div>
        @else
            <form wire:submit="submit" class="cv-form-card" enctype="multipart/form-data">
                <div class="field-row">
                    <input type="text" wire:model="prenom" placeholder="{{ __('cv_services.form.fields.prenom') }}" />
                    <input type="text" wire:model="nom" placeholder="{{ __('cv_services.form.fields.nom') }}" />
                </div>
                @error('prenom') <small class="footer-newsletter-error">{{ $message }}</small> @enderror
                @error('nom') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <div class="field-row">
                    <input type="email" wire:model="email" placeholder="{{ __('cv_services.form.fields.email') }}" />
                    <input type="text" wire:model="telephone" placeholder="{{ __('cv_services.form.fields.telephone') }}" />
                </div>
                @error('email') <small class="footer-newsletter-error">{{ $message }}</small> @enderror
                @error('telephone') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <div class="field-row">
                    <input type="text" wire:model="whatsapp" placeholder="{{ __('cv_services.form.fields.whatsapp') }}" />
                    <input type="text" wire:model="pays" placeholder="{{ __('cv_services.form.fields.pays') }}" />
                </div>
                @error('whatsapp') <small class="footer-newsletter-error">{{ $message }}</small> @enderror
                @error('pays') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <div class="field-row">
                    <input type="text" wire:model="ville" placeholder="{{ __('cv_services.form.fields.ville') }}" />
                    <input type="date" wire:model="dateNaissance" placeholder="{{ __('cv_services.form.fields.date_naissance') }}" />
                </div>
                @error('ville') <small class="footer-newsletter-error">{{ $message }}</small> @enderror
                @error('dateNaissance') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <div class="field-row">
                    <select wire:model="genre">
                        <option value="">{{ __('cv_services.form.fields.genre_placeholder') }}</option>
                        <option value="homme">{{ __('cv_services.form.genders.homme') }}</option>
                        <option value="femme">{{ __('cv_services.form.genders.femme') }}</option>
                        <option value="non_precise">{{ __('cv_services.form.genders.non_precise') }}</option>
                    </select>
                    <input type="text" wire:model="titrePoste" placeholder="{{ __('cv_services.form.fields.titre_poste') }}" />
                </div>
                @error('genre') <small class="footer-newsletter-error">{{ $message }}</small> @enderror
                @error('titrePoste') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <div class="field-row">
                    <input type="text" wire:model="niveauEtude" placeholder="{{ __('cv_services.form.fields.niveau_etude') }}" />
                    <input type="text" wire:model="domaineEtude" placeholder="{{ __('cv_services.form.fields.domaine_etude') }}" />
                </div>
                @error('niveauEtude') <small class="footer-newsletter-error">{{ $message }}</small> @enderror
                @error('domaineEtude') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <div class="field-row">
                    <input type="number" min="0" max="60" wire:model="anneesExperience" placeholder="{{ __('cv_services.form.fields.annees_experience') }}" />
                    <select wire:model="typeContratRecherche">
                        <option value="cdi">{{ __('cv_services.contracts.cdi') }}</option>
                        <option value="cdd">{{ __('cv_services.contracts.cdd') }}</option>
                        <option value="stage">{{ __('cv_services.contracts.stage') }}</option>
                        <option value="freelance">{{ __('cv_services.contracts.freelance') }}</option>
                        <option value="tous">{{ __('cv_services.contracts.tous') }}</option>
                    </select>
                </div>
                @error('anneesExperience') <small class="footer-newsletter-error">{{ $message }}</small> @enderror
                @error('typeContratRecherche') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <textarea wire:model="competences" rows="3" placeholder="{{ __('cv_services.form.fields.competences') }}"></textarea>
                @error('competences') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <textarea wire:model="langues" rows="3" placeholder="{{ __('cv_services.form.fields.langues') }}"></textarea>
                @error('langues') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <textarea wire:model="objectifProfessionnel" rows="4" placeholder="{{ __('cv_services.form.fields.objectif') }}"></textarea>
                @error('objectifProfessionnel') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <textarea wire:model="secteursInteret" rows="3" placeholder="{{ __('cv_services.form.fields.secteurs_interet') }}"></textarea>
                @error('secteursInteret') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <div class="field-row">
                    <input type="url" wire:model="linkedinUrl" placeholder="{{ __('cv_services.form.fields.linkedin_url') }}" />
                    <input type="url" wire:model="portfolioUrl" placeholder="{{ __('cv_services.form.fields.portfolio_url') }}" />
                </div>
                @error('linkedinUrl') <small class="footer-newsletter-error">{{ $message }}</small> @enderror
                @error('portfolioUrl') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <label class="offers-check">
                    <input type="checkbox" wire:model="teletravailSouhaite" />
                    <span>{{ __('cv_services.form.fields.teletravail') }}</span>
                </label>

                <textarea wire:model="message" rows="4" placeholder="{{ __('cv_services.form.fields.message') }}"></textarea>
                @error('message') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <div class="cv-services-checks">
                    <label class="offers-check">
                        <input type="checkbox" wire:model="demandeRedactionCv" />
                        <span>{{ __('cv_services.form.fields.demande_redaction_cv') }}</span>
                    </label>
                    <label class="offers-check">
                        <input type="checkbox" wire:model="demandeCoaching" />
                        <span>{{ __('cv_services.form.fields.demande_coaching') }}</span>
                    </label>
                    <label class="offers-check">
                        <input type="checkbox" wire:model="demandeOrientation" />
                        <span>{{ __('cv_services.form.fields.demande_orientation') }}</span>
                    </label>
                </div>

                <label class="cv-file-field">
                    <span>{{ __('cv_services.form.fields.cv_fichier') }}</span>
                    <input type="file" wire:model="cvFichier" accept="application/pdf,.pdf" />
                </label>
                @error('cvFichier') <small class="footer-newsletter-error">{{ $message }}</small> @enderror

                <div class="contact-form-actions">
                    <button type="submit" class="solid-submit" wire:loading.attr="disabled">{{ __('cv_services.form.submit') }}</button>
                    <a href="{{ auth()->user()?->canManageOffers() ? route('dashboard') : route('panel.user.cv-depots') }}" class="ghost-submit">{{ __('cv_services.form.space_cta') }}</a>
                </div>

                <div wire:loading wire:target="submit,cvFichier" class="footer-newsletter-success">
                    {{ __('cv_services.form.loading') }}
                </div>
            </form>
        @endguest
    </div>
</div>
