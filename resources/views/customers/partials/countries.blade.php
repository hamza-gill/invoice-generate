@php
$countries = ['United States', 'United Kingdom', 'Canada', 'Australia', 'India', 'Germany', 'France', 'Italy', 'Spain', 'Netherlands', 'Belgium', 'Switzerland', 'Austria', 'Ireland', 'Portugal', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Poland', 'Czech Republic', 'Greece', 'Hungary', 'Romania', 'Bulgaria', 'Croatia', 'Slovakia', 'Slovenia', 'Lithuania', 'Latvia', 'Estonia', 'Luxembourg', 'Iceland', 'Malta', 'Cyprus', 'Ukraine', 'Russia', 'Turkey', 'United Arab Emirates', 'Saudi Arabia', 'Qatar', 'Kuwait', 'Bahrain', 'Oman', 'Jordan', 'Lebanon', 'Israel', 'Egypt', 'South Africa', 'Nigeria', 'Kenya', 'Ghana', 'Morocco', 'Algeria', 'Tunisia', 'Brazil', 'Mexico', 'Argentina', 'Chile', 'Colombia', 'Peru', 'Venezuela', 'Ecuador', 'Uruguay', 'Paraguay', 'Bolivia', 'China', 'Japan', 'South Korea', 'Singapore', 'Malaysia', 'Indonesia', 'Thailand', 'Vietnam', 'Philippines', 'Pakistan', 'Bangladesh', 'Sri Lanka', 'Nepal', 'New Zealand', 'Fiji', 'Hong Kong', 'Taiwan'];
@endphp
@foreach($countries as $country)
    <option value="{{ $country }}" {{ old('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
@endforeach
