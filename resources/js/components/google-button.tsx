import { GoogleIconSvg } from './svg/google-icon-svg';
import { Button } from './ui/button';

const GoogleButton = () => {
  return (
    <Button asChild>
      <a href={route('auth.redirect')}>
        <GoogleIconSvg />
        Sign in with Google
      </a>
    </Button>
  );
};

export default GoogleButton;
