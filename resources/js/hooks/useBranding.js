import { useEffect, useState } from 'react';
import api from '../api';

export function useBranding() {
  const [branding, setBranding] = useState({
    center_name: 'Lancet - Medical Services',
    logo_url: '',
  });

  useEffect(() => {
    api.get('/public-settings')
      .then(({ data }) => setBranding((prev) => ({ ...prev, ...data })))
      .catch(() => {});
  }, []);

  return branding;
}
