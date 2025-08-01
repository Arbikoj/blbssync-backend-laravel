export type User = {
    id: number;
    name: string;
    email: string;
};

export type UserFormData = {
    id?: number;
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
};
