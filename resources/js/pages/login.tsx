import React from "react";
import { Head, useForm } from "@inertiajs/react";
import { Lock, Mail, RefreshCw, School, LogIn } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from "@/components/ui/card";
import { Alert, AlertDescription } from "@/components/ui/alert";

export default function Login() {
  const { data, setData, post, processing, errors } = useForm({
    email: "",
    password: "",
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post("/login");
  };

  return (
    <>
      <Head>
        <title>Login Admin - Portal Kelulusan</title>
        <meta name="description" content="Halaman masuk portal administrator kelulusan sekolah." />
      </Head>

      <div className="min-h-screen bg-brand-soft-bg/30 px-4 py-8 flex items-center justify-center font-sans">
        <div className="w-full max-w-md space-y-6">
          
          <Card className="border-none shadow-xl bg-white rounded-2xl overflow-hidden">
            
            {/* Header / Brand Panel with Gradient */}
            <div className="p-8 text-white text-center relative overflow-hidden bg-linear-to-br from-brand-primary via-brand-secondary to-brand-accent">
              <div className="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-8 -mt-8 pointer-events-none" />
              <div className="absolute bottom-0 left-0 w-24 h-24 bg-black/5 rounded-full blur-xl -ml-8 -mb-8 pointer-events-none" />
              
              <div className="relative flex flex-col items-center space-y-3">
                <div className="p-2.5 bg-white/10 backdrop-blur-md rounded-xl border border-white/20 shadow-inner">
                  <School className="w-8 h-8 text-white" />
                </div>
                <div>
                  <h2 className="text-sm font-semibold tracking-wider uppercase text-brand-soft-bg">
                    SMP ISLAM PARLAUNGAN
                  </h2>
                  <h1 className="text-2xl font-extrabold tracking-tight mt-1">
                    Portal Admin
                  </h1>
                </div>
              </div>
            </div>

            <CardContent className="p-6 md:p-8 pt-8">
              <form onSubmit={handleSubmit} className="space-y-5">
                
                {/* General/Email error message */}
                {errors.email && (
                  <Alert variant="destructive" className="rounded-xl bg-rose-50/50 border-rose-200 p-4">
                    <AlertDescription className="text-xs font-semibold text-rose-700">
                      {errors.email}
                    </AlertDescription>
                  </Alert>
                )}

                <div className="space-y-1.5">
                  <Label htmlFor="email" className="text-slate-600 font-semibold">
                    Email Administrator
                  </Label>
                  <div className="relative flex items-center">
                    <div className="absolute left-3 text-slate-400 pointer-events-none">
                      <Mail className="w-4 h-4" />
                    </div>
                    <Input
                      id="email"
                      type="email"
                      required
                      placeholder="admin@sekolah.sch.id"
                      value={data.email}
                      onChange={(e) => setData("email", e.target.value)}
                      disabled={processing}
                      className="pl-10 text-slate-700 border-slate-200 placeholder:text-slate-400 focus-visible:ring-brand-primary h-11 rounded-lg"
                    />
                  </div>
                </div>

                <div className="space-y-1.5">
                  <Label htmlFor="password" className="text-slate-600 font-semibold">
                    Kata Sandi (Password)
                  </Label>
                  <div className="relative flex items-center">
                    <div className="absolute left-3 text-slate-400 pointer-events-none">
                      <Lock className="w-4 h-4" />
                    </div>
                    <Input
                      id="password"
                      type="password"
                      required
                      placeholder="••••••••"
                      value={data.password}
                      onChange={(e) => setData("password", e.target.value)}
                      disabled={processing}
                      className="pl-10 text-slate-700 border-slate-200 placeholder:text-slate-400 focus-visible:ring-brand-primary h-11 rounded-lg"
                    />
                  </div>
                  {errors.password && (
                    <p className="text-xs text-rose-500 font-medium mt-1">{errors.password}</p>
                  )}
                </div>

                <Button
                  type="submit"
                  disabled={processing}
                  className="w-full h-11 text-base font-semibold shadow-md bg-brand-primary hover:bg-brand-primary/95 text-white flex items-center justify-center gap-2 rounded-lg cursor-pointer disabled:cursor-not-allowed transition-all mt-6"
                >
                  {processing ? (
                    <>
                      <RefreshCw className="w-4 h-4 animate-spin" />
                      Mencoba Masuk...
                    </>
                  ) : (
                    <>
                      <LogIn className="w-4 h-4" />
                      Masuk Admin
                    </>
                  )}
                </Button>

              </form>
            </CardContent>

            <CardFooter className="bg-slate-50 p-4 border-t border-slate-100 flex items-center justify-center text-[10px] text-slate-400">
              Pusat Data Kelulusan Resmi © 2026.
            </CardFooter>

          </Card>
          
        </div>
      </div>
    </>
  );
}
