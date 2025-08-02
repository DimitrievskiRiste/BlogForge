export type AllowedTypes = "dbhost" | "dbuser" | "dbpass" | "dbname" | "dbport";
export type DBFormData = Partial<Record<AllowedTypes, string | number | null >>
