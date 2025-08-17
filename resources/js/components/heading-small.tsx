import { ReactNode } from 'react';

type Props = {
  title: string;
  description?: string;
  actions?: ReactNode;
};

export default function HeadingSmall({ title, description, actions }: Props) {
  return (
    <div className="flex justify-between">
      <header>
        <h3 className="mb-0.5 text-base font-medium">{title}</h3>
        {description && <p className="text-sm text-muted-foreground">{description}</p>}
      </header>

      {actions}
    </div>
  );
}
